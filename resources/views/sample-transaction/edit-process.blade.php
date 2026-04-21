@extends('master.layout')

@section('content')
    <style>
        .hover-preview {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
        }

        .hover-preview:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .hover-preview button {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .hover-preview:hover button {
            opacity: 1;
        }
    </style>
    @include('shared.appbar', ['backRoute' => 'sampleTransactionView', 'title' => 'Finish Sample Transaction Process'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('sampleTransactionEditProcess', $data->id) }}" enctype="multipart/form-data">
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
                <div class="mb-4">
                    <div class="font-bold mb-1">Sales Order Number (SO)</div>
                    <div class="ui input w-full !cursor-default opacity-70">
                        <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                            {{ $data->sample->so_number }}
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="font-bold mb-1">Process</div>
                    <div class="ui input w-full !cursor-default opacity-70">
                        <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                            {{ $data->process_name }}
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="!text-base">Finish Note</label>
                    <textarea style="resize: none;" name="finish_note" placeholder="Finish Note" rows="3">{{ $data->finish_note }}</textarea>
                </div>

                <button class="ui button customButton mt-4" type="submit">Finish</button>
            </form>
        </div>
    </div>

    <script>
        const existingFile = "{{ $data->filepath ? asset('storage/' . $data->filepath) : '' }}";
    </script>

    <script>
        $(document).ready(function () {
            $('#processesDropdown').dropdown();
        });
    </script>
@endsection
