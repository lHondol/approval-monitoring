@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'sampleTransactionView', 'title' => 'Approve Sample Transaction'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('sampleTransactionApprove', $data->id) }}" enctype="multipart/form-data">
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
                    <label class="!text-base">Approval Note</label>
                    <textarea style="resize: none;" name="picture_received_note" placeholder="Approval Note" rows="3"></textarea>
                </div>

                <button class="ui button customButton mt-4" type="submit">Approve</button>
            </form>
        </div>
    </div>
@endsection
