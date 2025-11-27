@php
    function isActive($path) {
        return request()->is($path);
    }

    function navClass($routeName) {
        return isActive($routeName)
            ? 'text-xl w-full border-r-4 border-b-2 !pl-0'
            : '!pl-3';
    }
@endphp

<style>
    .sidebar a:hover {
        color: var(--primary-color); /* or your own color */
        text-decoration: none;
    }
</style>

<div class="sidebar w-[250px] pr-0">
    <h1 class="text-3xl font-bold uppercase wrap-break-word">
        {{ config('app.name', 'Laravel') }}
    </h1>

    <div class="mt-8 w-full">
        <a href="{{ route('dashboard') }}" class="{{ navClass('dashboard') }}">
            Dashboard
        </a>

        <a href="{{ route('drawingTransactionView') }}" class="{{ navClass('drawing-transaction*') }}">
            Drawing Transactions
        </a>
    </div>
</div>
