<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="data:,">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- JQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    
    <!-- Fomantic UI CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.4/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.4/dist/semantic.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.semanticui.css">
    <script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.5/js/dataTables.semanticui.js"></script>

    <style>
        /* Content */
        .content {
            flex: 1;
            padding: 20px;
            background: var(--base-bg);
        }
    </style>
</head>
<body>

    @include('master.sidebar')

    <div class="content">
        @yield('content')
    </div>

    @stack('scripts')

</body>
</html>
