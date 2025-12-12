@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'customerView', 'title' => 'Create New Customer', 'hideBackButton' => true])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('passwordChange') }}">
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

                <div class="field flex-1">
                    <label class="!text-base">Current Password</label>
                    <input type="password" name="current_password" placeholder="Password">
                </div>

                <div class="field flex-1">
                    <label class="!text-base">New Password</label>
                    <input type="password" name="password" placeholder="New Password">
                </div>

                <div class="field flex-1">
                    <label class="!text-base">Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="Confirm New Password">
                </div>

                <button class="ui button customButton mt-4" type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection
