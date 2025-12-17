<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="data:,">
        
        <!-- Tailwind -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Fomantic UI -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.4/dist/semantic.min.css">
        <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.4/dist/semantic.min.js"></script>

        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    </head>
    <body class="flex justify-center items-center !bg-[var(--base-bg)]">
        <div class="ui card md:min-w-[500px]">
            <div class="content min-h-[300px] flex flex-col items-center !p-10">
                <h1 class="text-2xl font-bold text-[var(--primary-color)] uppercase text-center">{{ config('app.name', 'Laravel') }}</h1>
                <span>Please log in to continue</span>
                <form class="ui form w-full p-3" method="post" action="{{ route('login') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="ui negative message">
                            <div class="header">We had some issues</div>
                            <ul class="list">
                            @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                            @endforeach 
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
                            <input type="checkbox" tabindex="0" class="hidden" name="remember_me">
                            <label class="!text-base">Remember Me</label>
                        </div>
                    </div>
                    <div class="field flex flex-col gap-2">
                        <span>Don't have account yet? <a 
                            style="text-decoration: underline;"
                        href="{{ route('registerForm') }}">Register Here</a></span>
                        <span>Forgot password? <a 
                            style="text-decoration: underline;"
                        href="{{ route('passwordForgotForm') }}">Reset Here</a></span>
                    </div>
                    <button class="ui button w-full customButton" type="submit">Login</button>
                </form>
            </div>
        </div>
    </body>

    <script>
        $(function () {
            $('.ui.checkbox').checkbox();

            @if (session('passwordChanged'))
                alert(@json(session('passwordChanged')));
            @endif
        });
    </script>
</html>
