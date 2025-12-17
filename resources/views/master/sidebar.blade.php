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
    .hamburger {
        background-color: var(--sidebar-bg);
        color: white;
    }
</style>

<div>
    <div class="md:hidden hamburger">
        <h1 class="text-2xl md:text-3xl font-bold uppercase p-5
                flex items-center gap-3 shrink-0">
            <i class="hamburger icon md:!hidden" id="hamburgerBtn"></i>
            {{ config('app.name', 'Laravel') }}
        </h1>
    </div>

    <div id="sidebarContent" class="sidebar hidden md:flex w-full md:w-[250px] !p-0
            h-fit
            md:h-screen
            flex-col overflow-hidden">

        <h1 class="hidden md:flex text-3xl font-bold uppercase p-5
                items-center gap-3 shrink-0 cursor-pointer">
            {{ config('app.name', 'Laravel') }}
        </h1>

        <div class="flex flex-col flex-1 min-h-0">

            <div class="flex-1 overflow-y-auto text-xl min-h-0">
                <a href="{{ route('dashboard') }}" class="{{ navClass('dashboard') }}">
                    Dashboard
                </a>

                @if (auth()->user()->hasAnyPermission(['view_drawing_transaction', 'view_distributed_drawing_transaction']))
                    <a href="{{ route('drawingTransactionView') }}" class="{{ navClass('drawing-transactions*') }}">
                        Drawing Transactions
                    </a>
                @endif

                @if (auth()->user()->hasPermissionTo('view_customer'))
                    <a href="{{ route('customerView') }}" class="{{ navClass('customers*') }}">
                        Customers
                    </a>
                @endif

                @if (auth()->user()->hasAnyPermission(['view_user', 'view_role']))
                    <div class="relative">
                        <button
                            class="parentMenu w-full text-left !flex justify-between items-center {{ navParentClass(['users*', 'roles*']) }}"
                            onclick="document.getElementById('userManagementMenu').classList.toggle('hidden');
                                    document.getElementById('userManagementIcon').classList.toggle('-rotate-90');">
                            <span>User Management</span>
                            <i id="userManagementIcon"
                            class="angle left icon transition-transform duration-200
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

                <a href="{{ route('passwordChangeForm') }}" class="{{ navClass('password*') }}">
                    Change Password
                </a>
            </div>

            <div class="bg-gray-400 bg-opacity-30 shrink-0">
                <div class="px-5 pt-5 text-md text-center">Logged as</div>
                <div class="p-5 text-md text-center">{{ auth()->user()->name }}</div>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-xl w-full p-5 text-left hover:bg-red-500 text-center">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function isMobile() {
        return window.innerWidth < 768;
    }

    function resetSidebarOnResize() {
        if (!isMobile()) {
            $('#sidebarContent')
                .stop(true, true)
                .removeAttr('style')
                .show();
        } else {
            $('#sidebarContent').hide();
        }
    }

    $('#sidebarContent a').on('click', function () {
        if (isMobile()) {
            $('#sidebarContent').slideUp(250);
        }
    });

    $('.parentMenu').on('click', function () {
        if (isMobile()) {
        }
    });

    $('#hamburgerBtn').on('click', function () {
        $('#sidebarContent').stop(true, true).slideToggle(250);
    });
    
    $(window).on('resize', resetSidebarOnResize);
    resetSidebarOnResize();
</script>