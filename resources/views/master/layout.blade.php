<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="data:,">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- JQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    
    <!-- Fomantic UI CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.4/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.4/dist/semantic.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.semanticui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.5/css/buttons.semanticui.css">
    <script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.5/js/dataTables.semanticui.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.5/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.semanticui.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.colVis.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.2/css/fixedColumns.dataTables.min.css">
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.2/js/dataTables.fixedColumns.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <style>
        /* Content */
        .content {
            flex: 1;
            padding: 20px;
            background: var(--base-bg);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row">

    @include('master.sidebar')

    <div class="content flex-1 overflow-auto">
        @yield('content')
    </div>

    @stack('scripts')

    <script>
        $(document).on('mouseenter', '.dt-button-collection', function() {
            const $menu = $(this);
            $menu.css('width', 'auto'); // let width fit content
        });
    </script>

</body>
</html>
