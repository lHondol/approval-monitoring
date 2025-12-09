@php
    use App\Enums\StatusDrawingTransaction;
    use Carbon\Carbon;

    $renderStatusColor = function ($status) {
        return match ($status) {
            StatusDrawingTransaction::WAITING_1ST_APPROVAL->value => "teal",
            StatusDrawingTransaction::WAITING_2ND_APPROVAL->value => "orange",
            StatusDrawingTransaction::REVISE_NEEDED->value        => "yellow",
            StatusDrawingTransaction::DISTRIBUTED->value          => "purple",
        };
    };
@endphp

<div class="flex justify-center">
    <div class="ui card !w-[800px] !p-8">
        <div class="mb-4">
            <div class="font-bold mb-1">Status</div>
            <div class="ui input w-full !cursor-default">
                <div class="w-full px-5 py-5 !flex gap-3 flex-wrap rounded bg-gray-100 !text-black">
                    <div class="flex gap-2 items-center">
                        @if ($data->as_additional_data)
                            <span class="ui green label">Additional Data</span>
                        @endif
                        @if ($data->done_revised)
                            <span class="ui green label">Revised</span>
                        @endif
                        <span class="ui label {{ $renderStatusColor($data->status) }}">
                            {{ $data->status }}
                        </span>
                    </div>
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
            <div class="font-bold mb-1">Sales Order Number (SO)</div>
            <div class="ui input w-full !cursor-default opacity-70">
                <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                    {{ $data->so_number ?? '-- Not Input Yet --' }}
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="font-bold mb-1">Purchase Order Number (PO)</div>
            <div class="ui input w-full !cursor-default opacity-70">
                <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                    {{ $data->po_number }}
                </div>
            </div>
        </div>

        @if ($data->as_additional_data)
            <div class="mb-4">
                <div class="font-bold mb-1">Additional Data Note</div>
                <div class="ui input w-full !cursor-default opacity-70">
                   <div class="w-full px-5 rounded bg-gray-100 !text-black py-2 min-h-[80px]">
                        {{ $data->additional_data_note }}
                    </div>
                </div>
            </div>
        @endif

        @if ($data->need_revise_note && $data->status === StatusDrawingTransaction::REVISE_NEEDED->value)
            <div class="mb-4">
                <div class="font-bold mb-1">Need Revise Note</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 rounded bg-gray-100 !text-black py-2 min-h-[80px]">
                        {{ $data->need_revise_note }}
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-4">
            <div class="font-bold mb-1">Description</div>
            <div class="ui input w-full !cursor-default opacity-70">
                <div class="w-full px-5 rounded bg-gray-100 !text-black py-2 min-h-[80px]">
                    {{ $data->description }}
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="font-bold mb-1">Created At</div>
            <div class="ui input w-full !cursor-default opacity-70">
                <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                    {{ Carbon::parse($data->created_at)->format('d M Y H:i:s') }}
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="font-bold mb-1">Distributed At</div>
            <div class="ui input w-full !cursor-default opacity-70">
                <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                    {{ $data->distributed_at
                        ? Carbon::parse($data->distributed_at)->format('d M Y H:i:s')
                        : '-- Not Distribute Yet --'
                    }}
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="font-bold mb-1">Uploaded Files</div>

            <div class="ui input w-full !cursor-default opacity-70">
                <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                    <div id="previewContainer"
                         class="flex flex-wrap gap-3"
                         style="min-height:150px;">
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
</script>

<script>
    document.addEventListener('DOMContentLoaded', async function () {
        const previewContainer = document.getElementById('previewContainer');

        let files = @json($data->filepath);
        if (!Array.isArray(files)) files = [files];

        for (let filePath of files) {
            const fileUrl = "{{ asset('storage') }}/" + filePath;

            const typedArray = await fetch(fileUrl).then(res => res.arrayBuffer()).then(buffer => new Uint8Array(buffer));
            const pdf = await pdfjsLib.getDocument(typedArray).promise;
            const page = await pdf.getPage(1);

            const fixedWidth = 120;
            const fixedHeight = 150;
            const viewport = page.getViewport({ scale: 1 });

            const scale = Math.min(fixedWidth / viewport.width, fixedHeight / viewport.height);
            const scaledViewport = page.getViewport({ scale });

            const canvas = document.createElement('canvas');
            canvas.width = fixedWidth;
            canvas.height = fixedHeight;

            const context = canvas.getContext('2d');
            context.fillStyle = '#fff';
            context.fillRect(0, 0, fixedWidth, fixedHeight);

            const offsetX = (fixedWidth - scaledViewport.width) / 2;
            const offsetY = (fixedHeight - scaledViewport.height) / 2;

            await page.render({
                canvasContext: context,
                viewport: scaledViewport,
                transform: [1, 0, 0, 1, offsetX, offsetY]
            }).promise;

            const wrapper = document.createElement('div');
            wrapper.className = 'pdf-wrapper';
            wrapper.style.width = fixedWidth + 'px';
            wrapper.style.height = fixedHeight + 'px';
            wrapper.appendChild(canvas);

            wrapper.addEventListener('click', () => {
                window.open(fileUrl, '_blank');
            });

            previewContainer.appendChild(wrapper);
        }
    });
</script>
