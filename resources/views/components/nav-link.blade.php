@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-toffee-300 text-sm font-medium leading-5 text-alabaster-100 focus:outline-none focus:border-toffee-400 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-alabaster-200 hover:text-alabaster-100 hover:border-juniper-500 focus:outline-none focus:text-alabaster-100 focus:border-juniper-500 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
