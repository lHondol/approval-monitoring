@php
    function isActive($path, $except = [])
    {
        $request = request();
        foreach ($except as $ex) {
            if ($request->is($ex)) {
                return false;
            }
        }

        return $request->is($path);
    }

    function navClass($routeName, $except = []) {
        return isActive($routeName, $except)
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

    .childMenu {
        padding-left: calc(25px + var(--indent, 0px)) !important;
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
                @if (auth()->user()->hasAnyPermission(['view_prerelease_so_transaction', 'view_sample_transaction']))
                    <div class="relative">

                        <button
                            class="parentMenu w-full text-left !flex justify-between items-center {{ navParentClass(['prerelease-so-transactions*', 'sample-transactions*']) }}"
                            onclick="
                                document.getElementById('dashboardMenu').classList.toggle('hidden');
                                document.getElementById('dashboardIcon').classList.toggle('-rotate-90');
                            "
                        >
                            <span>Dashboard</span>
                            <i id="dashboardIcon"
                            class="angle left icon transition-transform duration-200
                            {{ request()->is('prerelease-so-transactions*') || request()->is('sample-transactions*') ? '-rotate-90' : '' }}">
                            </i>
                        </button>

                        <div id="dashboardMenu" class="{{ isParentOpen(['prerelease-so-transactions*', 'sample-transactions*']) }}">

                            @if (auth()->user()->hasAnyPermission(['view_prerelease_so_transaction']))
                                <a href="{{ route('prereleaseSoTransactionView') }}"
                                class="{{ navClass('prerelease-so-transactions*') }} !flex justify-between items-center childMenu">
                                    <span>SO Regular</span>
                                    <div id="prereleaseBadge"
                                        class="bg-yellow-600 min-w-[50px] text-center text-sm rounded"
                                        style="display:none;">
                                    </div>
                                </a>
                            @endif

                            @if (auth()->user()->hasAnyPermission(['view_sample_transaction']))
                                <div class="relative">

                                    <button
                                        class="parentMenu w-full text-left !flex justify-between items-center {{ navParentClass(['sample-transactions*']) }} childMenu"
                                        onclick="
                                            document.getElementById('sampleManagementMenu').classList.toggle('hidden');
                                            document.getElementById('sampleManagementIcon').classList.toggle('-rotate-90');
                                        "
                                    >
                                        <span>SO Sample Management</span>
                                        <i id="sampleManagementIcon"
                                        class="angle left icon transition-transform duration-200
                                        {{ request()->is('sample-transactions*') ? '-rotate-90' : '' }}">
                                        </i>
                                    </button>

                                    <div id="sampleManagementMenu" class="{{ isParentOpen(['sample-transactions*']) }}">
                                        <a href="{{ route('sampleTransactionView') }}"
                                        class="{{ navClass('sample-transactions*', ['sample-transactions/calendar*']) }} childMenu" style="--indent: 7px;">
                                            SO Sample
                                        </a>

                                        <a href="{{ route('sampleTransactionCalendarView') }}"
                                        class="{{ navClass('sample-transactions/calendar*') }} childMenu" style="--indent: 7px;">
                                            Calendar View
                                        </a>
                                    </div>

                                </div>
                            @endif

                        </div>
                    </div>
                @endif

                @if (auth()->user()->hasAnyPermission(['view_drawing_transaction', 'view_distributed_drawing_transaction']))
                    <a href="{{ route('reportingView') }}" class="{{ navClass('reportings*') }}">
                        Reportings
                    </a>
                @endif

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

                {{-- @if (auth()->user()->hasPermissionTo('view_area'))
                    <a href="{{ route('areaView') }}" class="{{ navClass('areas*') }}">
                        Areas
                    </a>
                @endif --}}

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
                                <a href="{{ route('userView') }}" class="{{ navClass('users*') }} childMenu">
                                    Users
                                </a>
                            @endif
                            @if (auth()->user()->hasPermissionTo('view_role'))
                                <a href="{{ route('roleView') }}" class="{{ navClass('roles*') }} childMenu">
                                    Roles
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <a href="{{ route('passwordChangeForm') }}" class="{{ navClass('password*') }}">
                    Change Password
                </a>

                <a href="{{ route('activityLogView') }}" class="{{ navClass('activity-logs*') }}">
                    Activity Logs
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

    function loadPrereleaseBadge() {
        $.ajax({
            url: "{{ route('prereleaseSoTransactionGetBadgeCount') }}",
            type: "GET",
            success: function (response) {
                const badge = $('#prereleaseBadge');

                if (response.count > 0) {
                    badge.text(response.count).show();
                } else {
                    badge.hide();
                }
            },
            error: function () {
                console.log('Failed to fetch badge count');
            }
        });
    }
</script>

@if (auth()->user()->hasAnyPermission(['view_prerelease_so_transaction']))
<script>
    $(document).ready(function () {
        loadPrereleaseBadge();

        // Refresh every 10 seconds
        setInterval(loadPrereleaseBadge, 10000);
    });
</script>
@endif