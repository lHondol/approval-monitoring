<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="data:,">

        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        
        <!-- Tailwind -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Fomantic UI -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.4/dist/semantic.min.css">
        <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.4/dist/semantic.min.js"></script>

    </head>
    <body class="flex justify-center items-center !bg-[var(--base-bg)]">
        <div class="ui card min-w-[500px]">
            <div class="content min-h-[300px] flex flex-col items-center !p-10">
                <h1 class="text-2xl font-bold text-[var(--primary-color)] uppercase">{{ config('app.name', 'Laravel') }}</h1>
                <span>Silakan login untuk melanjutkan</span>
                <form class="ui form w-full p-3" method="post" action="{{ route('login') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="ui negative message">
                            <div class="header">We had some issues</div>
                            <ul class="list">
                            <li>{{ $errors->first() }}</li>
                            </ul>
                        </div>
                    @endif

                    <div class="field">
                        <label class="!text-base">Email</label>
                        <input type="email" name="email" placeholder="Email">
                    </div>
                    <div class="field">
                        <label class="!text-base">Password</label>
                        <input type="password" name="password" placeholder="Password">
                    </div>
                    <div class="field">
                        <div class="ui checkbox">
                            <input type="checkbox" tabindex="0" class="hidden" name="rememberMe">
                            <label class="!text-base">Remember Me</label>
                        </div>
                    </div>
                    <button class="ui button w-full !bg-[var(--primary-color)] !text-white" type="submit">Login</button>
                </form>
            </div>
        </div>
    </body>

    <script>
        $('.ui.checkbox').checkbox();
    </script>
</html>
