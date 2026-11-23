<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-toffee-500 border border-transparent rounded-md font-semibold text-xs text-juniper-900 uppercase tracking-widest hover:bg-toffee-600 focus:bg-toffee-600 active:bg-toffee-700 focus:outline-none focus:ring-2 focus:ring-toffee-300 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
