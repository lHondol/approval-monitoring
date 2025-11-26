@extends('master.layout', ['active' => 'dashboard'])

@section('content')
    <div class="text-xl">Welcome to Approving Monitoring . . .</div>
    <div class="text-2xl font-bold">Mr./Ms./Mrs. {{ auth()->user()->name }}</div>
@endsection