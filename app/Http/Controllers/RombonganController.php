<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRombonganRequest;
use App\Http\Requests\StorePesertaRombonganRequest;
use App\Imports\PesertaRombonganImport;
use App\Models\PengajuanRombongan;
use App\Models\Pesanan;
use App\Models\PesertaRombongan;
use App\Models\TiketPaket;
use App\Services\HitungHargaService;
use App\Services\KuotaService;
use App\Services\TiketGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Exceptions\LaravelExcelException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetReaderException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RombonganController extends Controller
{
    private const DRAFT_KEY = 'pengajuan_rombongan_draft';

    public function create(Request $request): RedirectResponse
    {
        $request->session()->forget(self::DRAFT_KEY);

        $oldInput = $request->session()->get('_old_input', []);
        $message = $request->session()->get('message');
        $query = ['rombongan' => 1];
        if ($request->integer('tiket_paket_id')) {
            $query['tiket_paket_id'] = $request->integer('tiket_paket_id');
        }

        $redirect = redirect()->to(route('user.booking', $query).'#paket-rombongan');

        if ($oldInput !== []) {
            $redirect->withInput($oldInput);
        }

        if ($message) {
            $redirect->with('message', $message);
        }

        return $redirect;
    }

    public function periksa(
        StorePengajuanRombonganRequest $request,
        HitungHargaService $harga,
        KuotaService $kuota,
    ): RedirectResponse {
        $data = $request->validated();
        $paket = TiketPaket::aktif()
            ->kategori(TiketPaket::KATEGORI_ROMBONGAN)
            ->find($data['tiket_paket_id']);

        if (! $paket) {
            throw ValidationException::withMessages([
                'tiket_paket_id' => 'Paket rombongan yang dipilih sudah tidak tersedia.',
            ]);
        }

        if ($kuota->tanggalTutup($data['tanggal_kunjungan'])) {
            throw ValidationException::withMessages([
                'tanggal_kunjungan' => 'Tanggal yang dipilih sedang tutup. Silakan pilih tanggal lain.',
            ]);
        }

        if (! $kuota->cukup($paket, $data['tanggal_kunjungan'], (int) $data['jumlah_tiket'], $request->user()->id)) {
            throw ValidationException::withMessages([
                'jumlah_tiket' => 'Kuota harian tidak mencukupi. Sisa kuota: '.$kuota->sisa($paket, $data['tanggal_kunjungan'], $request->user()->id).' tiket.',
            ]);
        }

        $diskon = $harga->diskonTerbaik(
            $paket,
            (int) $data['jumlah_tiket'],
            Carbon::parse($data['tanggal_kunjungan']),
        );

        $request->session()->put(self::DRAFT_KEY, [
            ...$data,
            'diskon_id' => $diskon?->id,
            'diskon_nama' => $diskon?->nama,
            'harga_satuan' => $paket->harga,
            ...$harga->hitung($paket, (int) $data['jumlah_tiket'], $diskon),
        ]);

        return redirect()->to(route('user.booking').'#paket-rombongan')
            ->with('message', 'Data kunjungan tersimpan. Lengkapi biodata peserta untuk melanjutkan.');
    }

    public function peserta(Request $request): RedirectResponse
    {
        $draft = $request->session()->get(self::DRAFT_KEY);

        if (! $draft) {
            return redirect()->to(route('user.booking').'#paket-rombongan')
                ->with('error', 'Lengkapi data rombongan terlebih dahulu.');
        }

        return redirect()->to(route('user.booking').'#paket-rombongan');
    }

    public function simpanPeserta(
        StorePesertaRombonganRequest $request,
        HitungHargaService $harga,
        KuotaService $kuota,
        TiketGenerator $generator,
    ): RedirectResponse {
        $draft = $request->session()->get(self::DRAFT_KEY);

        if (! $draft) {
            return to_route('pesan.rombongan.create')
                ->with('error', 'Sesi pengajuan berakhir. Silakan isi kembali data rombongan.');
        }

        $peserta = $this->ambilPeserta($request);
        $jumlah = (int) $draft['jumlah_tiket'];

        if ($peserta->count() !== $jumlah) {
            throw ValidationException::withMessages([
                'file_peserta' => 'Jumlah biodata peserta ('.$peserta->count().') harus sama dengan jumlah tiket yang diajukan ('.$jumlah.').',
            ]);
        }

        if ($peserta->contains(fn (array $baris) => $baris['nama'] === '')) {
            throw ValidationException::withMessages([
                'file_peserta' => 'Setiap peserta harus memiliki nama.',
            ]);
        }

        $pesanan = DB::transaction(function () use ($request, $draft, $peserta, $jumlah, $harga, $kuota, $generator) {
            $paket = TiketPaket::whereKey($draft['tiket_paket_id'])->lockForUpdate()->firstOrFail();

            if (! $paket->status_aktif || $paket->kategori !== TiketPaket::KATEGORI_ROMBONGAN) {
                throw ValidationException::withMessages([
                    'tiket_paket_id' => 'Paket rombongan ini sudah tidak tersedia.',
                ]);
            }

            if ($kuota->tanggalTutup($draft['tanggal_kunjungan'])) {
                throw ValidationException::withMessages([
                    'tanggal_kunjungan' => 'Tanggal yang dipilih sedang tutup. Silakan pilih tanggal lain.',
                ]);
            }

            if (! $kuota->cukup($paket, $draft['tanggal_kunjungan'], $jumlah, $request->user()->id)) {
                throw ValidationException::withMessages([
                    'jumlah_tiket' => 'Kuota harian tidak lagi mencukupi. Silakan pilih tanggal lain.',
                ]);
            }

            $diskon = $harga->diskonTerbaik(
                $paket,
                $jumlah,
                Carbon::parse($draft['tanggal_kunjungan']),
            );

            $pesanan = Pesanan::create([
                'kode_pesanan' => $generator->kodePesanan(),
                'user_id' => $request->user()->id,
                'tiket_paket_id' => $paket->id,
                'diskon_id' => $diskon?->id,
                'kategori' => TiketPaket::KATEGORI_ROMBONGAN,
                'tanggal_kunjungan' => $draft['tanggal_kunjungan'],
                'jumlah_tiket' => $jumlah,
                ...$harga->hitung($paket, $jumlah, $diskon),
                'status' => Pesanan::STATUS_MENUNGGU_PERSETUJUAN,
            ]);

            $pesanan->pengajuanRombongan()->create([
                'nama_sekolah' => $draft['nama_sekolah'],
                'penanggung_jawab' => $draft['penanggung_jawab'],
                'no_hp' => $draft['no_hp'],
                'mode_tiket' => $draft['mode_tiket'],
                'status' => PengajuanRombongan::STATUS_DIAJUKAN,
            ]);

            $pesanan->pesertaRombongan()->createMany($peserta->all());

            return $pesanan;
        });

        $request->session()->forget(self::DRAFT_KEY);

        return to_route('user.orders', ['pesanan' => $pesanan->id])
            ->with('message', 'Pengajuan rombongan berhasil dikirim dan sedang menunggu tinjauan admin.');
    }

    public function template(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['nama', 'kelas']);
            fclose($output);
        }, 'template-peserta-rombongan.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function updatePeserta(Request $request, Pesanan $pesanan, PesertaRombongan $peserta): RedirectResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 403);
        abort_unless($peserta->pesanan_id === $pesanan->id, 404);

        if ($pesanan->tanggal_kunjungan->isPast() && ! $pesanan->tanggal_kunjungan->isToday()) {
            return back()->with('error', 'Nama peserta tidak dapat diubah setelah hari kunjungan lewat.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kelas' => ['nullable', 'string', 'max:50'],
        ]);

        $peserta->update([
            'nama' => trim($validated['nama']),
            'kelas' => isset($validated['kelas']) ? trim($validated['kelas']) ?: null : null,
        ]);

        return back()->with('message', 'Data peserta rombongan berhasil diperbarui.');
    }

    /**
     * @return Collection<int, array{nama: string, kelas: ?string}>
     */
    private function ambilPeserta(StorePesertaRombonganRequest $request): Collection
    {
        $data = $request->validated();

        if ($data['sumber_data'] === 'manual') {
            return collect($data['peserta'])
                ->map(fn (array $baris) => [
                    'nama' => trim($baris['nama']),
                    'kelas' => isset($baris['kelas']) ? trim($baris['kelas']) ?: null : null,
                ])
                ->values();
        }

        $file = $request->file('file_peserta');

        if (! $file) {
            throw ValidationException::withMessages([
                'file_peserta' => 'Pilih file biodata peserta.',
            ]);
        }

        try {
            $sheet = Excel::toCollection(new PesertaRombonganImport, $file)->first() ?? collect();
        } catch (LaravelExcelException|SpreadsheetReaderException $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'file_peserta' => 'File tidak dapat dibaca. Gunakan template CSV atau Excel yang disediakan.',
            ]);
        }

        // Buang baris header jika ada (deteksi baris pertama yang berisi "nama").
        $rows = $sheet->values();
        if ($rows->isNotEmpty()) {
            $first = $rows->first();
            $firstCell = strtolower(trim((string) ($first[0] ?? $first['nama'] ?? '')));

            // Anggap baris pertama sebagai header jika kolom pertama adalah label header yang umum.
            $isHeader = in_array($firstCell, ['nama', 'name', 'nama peserta', 'nama lengkap', 'nama siswa'], true);

            if ($isHeader) {
                $rows = $rows->slice(1)->values();
            }
        }

        return $rows
            ->map(function ($row) {
                $values = array_values(is_array($row) ? $row : $row->toArray());

                return [
                    'nama' => trim((string) ($values[0] ?? '')),
                    'kelas' => isset($values[1]) ? (trim((string) $values[1]) ?: null) : null,
                ];
            })
            ->filter(fn (array $peserta) => $peserta['nama'] !== '' || $peserta['kelas'] !== null)
            ->values();
    }
}
