<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-juniper-700 border border-juniper-600 rounded-md font-semibold text-xs text-alabaster-100 uppercase tracking-widest shadow-sm hover:bg-juniper-600 focus:outline-none focus:ring-2 focus:ring-toffee-300 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
