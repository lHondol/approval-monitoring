@php
    function isActive($path) {
        return request()->is($path);
    }

    function navClass($routeName) {
        return isActive($routeName)
            ? 'w-full bg-gray-400 bg-opacity-30'
            : '';
    }

    function navParentClass($patterns = []) {
        foreach ($patterns as $pattern) {
            if (request()->is($pattern)) {
                return 'w-full';
            }
        }
        return '';
    }

    function isParentOpen($patterns = []) {
        foreach ($patterns as $pattern) {
            if (request()->is($pattern)) {
                return 'block'; // show child menu
            }
        }
        return 'hidden'; // hide child menu
    }
@endphp

<style>
    a, .parentMenu {
        display: block;
        padding: 18px !important;
    }
    .sidebar a:hover, .parentMenu:hover {
        background-color: #e74c3c;
        text-decoration: none;
    }
</style>

<div class="sidebar w-[250px] !p-0">
    <h1 class="text-3xl font-bold uppercase wrap-break-word p-5">
        {{ config('app.name', 'Laravel') }}
    </h1>

    <div class="mt-3 w-full text-xl">
        <a href="{{ route('dashboard') }}" class="{{ navClass('dashboard') }}">
            Dashboard
        </a>

        @if (auth()->user()->hasPermissionTo('view_drawing_transaction'))
            <a href="{{ route('drawingTransactionView') }}" class="{{ navClass('drawing-transactions*') }}">
                Drawing Transactions
            </a>
        @endif

        @if (auth()->user()->hasPermissionTo('view_customer'))
            <a href="{{ route('customerView') }}" class="{{ navClass('customers*') }}">
                Customers
            </a>
        @endif

        {{-- User Management Menu --}}
        @if (auth()->user()->hasAnyPermission([
            'view_user',
            'view_role'
        ]))
            <div class="relative">
                <button 
                    class="parentMenu w-full text-left !flex justify-between items-center {{ navParentClass(['users*', 'roles*']) }}" 
                    onclick="let menu = document.getElementById('userManagementMenu'); 
                            menu.classList.toggle('hidden'); 
                            document.getElementById('userManagementIcon').classList.toggle('-rotate-90');">
                    <span>User Management</span>
                    <i id="userManagementIcon" class="angle left icon transition-transform duration-200
                        {{ request()->is('users*') || request()->is('roles*') ? '-rotate-90' : '' }}"></i>
                </button>

                <div id="userManagementMenu" class="{{ isParentOpen(['users*', 'roles*']) }}">
                    @if (auth()->user()->hasPermissionTo('view_user'))
                        <a href="{{ route('userView') }}" class="{{ navClass('users*') }}">
                            Users
                        </a>
                    @endif
                    @if (auth()->user()->hasPermissionTo('view_role'))
                        <a href="{{ route('roleView') }}" class="{{ navClass('roles*') }}">
                            Roles
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
