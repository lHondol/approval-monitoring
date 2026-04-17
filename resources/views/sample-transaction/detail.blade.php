@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'sampleTransactionView', 'title' => 'Detail Sample Transaction', 'marginButtom' => '!mb-3'])

    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <div class="mb-4">
                <div class="font-bold mb-1">Sales Order Number (SO)</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->so_number }}
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="font-bold mb-1">Customer Name</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->customer->name }}
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="font-bold mb-1">SO Created At</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->so_created_at }}
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="font-bold mb-1">Shipment Request</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->shipment_request }}
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="font-bold mb-1">Picture Received At</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->picture_received_at ?? '-- Not Receive Yet --' }}
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="font-bold mb-1">Approval Note</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                    {{ !$data->picture_received_at ? '-- Not Receive Yet --' : ($data->picture_received_note ?? '-') }}
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="font-bold mb-1">Uploaded File</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="flex flex-wrap gap-2" 
                    id="previewContainer_{{ $data->id }}" 
                    style="min-height: 150px;">
                </div>
                </div>
            </div>

            <div class="font-bold mb-1">Process</div>
            @if (count($data->processes) > 0)
                <div class="flex flex-col items-center">
                    @foreach ($data->processes as $p)
                        <div class="w-full ui !my-3 segment flex justify-between gap-3 !p-8 rounded shadow-sm border border-gray-200">
                            <div class="flex flex-col flex-wrap justify-center items-start gap-4">
                                <p class="m-0"><strong>Process Name:</strong> {{ $p->process_name }}</p>
                                <p class="m-0"><strong>Start At:</strong> {{ \Carbon\Carbon::parse($p->start_at)->format('d M Y H:i:s') }}</p>
                                <p class="m-0"><strong>Start Note:</strong> {{ $p->start_note ?? '-' }}</p>
                                <p class="m-0"><strong>Finish At:</strong> {{ $p->finish_at ? \Carbon\Carbon::parse($p->finish_at)->format('d M Y H:i:s') : '-' }}</p>
                                <p class="m-0"><strong>Finish Note:</strong> {{ $p->finish_note ?? '-' }}</p>
                            </div>
                            <div>
                                @if ($p->filepath)
                                    <div class="flex flex-wrap gap-2" 
                                        id="previewContainer_{{ $p->id }}" 
                                        style="min-height: 150px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mb-4">
                    <div class="ui input w-full !cursor-default opacity-70">
                        <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                            -- No Process Yet --
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    </script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        // Prepare data for JS
        const dataList = @json([[
            'id' => $data->id,
            'filepath' => $data->filepath
        ]]);

        // Call your function
        initPreviewFile(dataList);

        async function initFilePreview(dataList) {
            console.log(document.querySelectorAll('[id^="previewContainer_"]'))
            for (const dt of dataList) {

                if (!dt.filepath) continue;

                const container = document.getElementById('previewContainer_' + dt.id);
                if (!container) continue;

                const fileUrl = "/storage/" + dt.filepath;

                const wrapper = document.createElement('div');
                wrapper.classList.add('hover-preview');

                wrapper.style.width = "120px";
                wrapper.style.height = "150px";
                wrapper.style.cursor = "pointer";
                wrapper.style.borderRadius = "6px";
                wrapper.style.overflow = "hidden";
                wrapper.style.background = "#f5f5f5";

                const img = document.createElement('img');
                img.src = fileUrl;
                img.style.width = "100%";
                img.style.height = "100%";
                img.style.objectFit = "cover";

                wrapper.appendChild(img);

                wrapper.addEventListener('click', () => {
                    window.open(fileUrl, '_blank');
                });

                container.appendChild(wrapper);
            }
        }

        const fileData = @json($data->processes);
        initFilePreview(fileData);

    </script>
@endpush

