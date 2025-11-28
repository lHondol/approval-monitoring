@extends('master.layout', ['active' => 'dashboard'])

@section('content')
<div class="flex items-center justify-center min-h-[100%]">
    <div class="p-8 text-center rounded-2xl">
        <h1 class="text-3xl font-bold mb-4">
            Welcome to Your Dashboard
        </h1>

        <p class="text-gray-600 text-lg">
            You're all set! Use the menu on the left to start navigating the system.
        </p>
    </div>
</div>
@endsection