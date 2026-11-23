@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-juniper-600 bg-juniper-800 text-alabaster-100 focus:border-toffee-300 focus:ring-toffee-300 rounded-md shadow-sm']) }}>
