<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request, PembayaranService $pembayaran): JsonResponse
    {
        $payload = $request->all();

        $orderId = $payload['order_id'] ?? null;

        if (! $orderId) {
            return response()->json(['message' => 'order_id wajib dikirim'], 422);
        }

        $record = Pembayaran::where('order_id_gateway', $orderId)->first();

        if (! $record) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        if (! $this->signatureValid($payload)) {
            return response()->json(['message' => 'Signature tidak valid'], 403);
        }

        $pembayaran->proses($record, $payload);

        return response()->json(['message' => 'OK']);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function signatureValid(array $payload): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');

        if ($serverKey === '') {
            return false;
        }

        $expected = hash('sha512',
            ($payload['order_id'] ?? '')
            .($payload['status_code'] ?? '')
            .($payload['gross_amount'] ?? '')
            .$serverKey
        );

        return hash_equals($expected, (string) ($payload['signature_key'] ?? ''));
    }
}
