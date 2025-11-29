<style>
    .pdf-wrapper {
        position: relative;
        width: 120px;
        height: 150px;
        flex-shrink: 0;
        cursor: pointer;
        overflow: hidden;
        border-radius: 4px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .pdf-wrapper:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        cursor: pointer;
    }

    .pdf-wrapper::after {
        content: '';
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .pdf-wrapper:hover::after {
        opacity: 1;
    }
</style>

@php
    use App\Enums\StatusDrawingTransaction;
    use Carbon\Carbon;

    $renderStatusColor = function ($status) {
        return match ($status) {
          StatusDrawingTransaction::WAITING_1ST_APPROVAL->value  => "teal",
          StatusDrawingTransaction::WAITING_2ND_APPROVAL->value   => "orange",
          StatusDrawingTransaction::REVISE_NEEDED->value   => "yellow",
          StatusDrawingTransaction::DISTRIBUTED->value   => "purple",
        };
    };
@endphp

<div class="flex justify-center">
<div class="ui card !w-[800px] !p-8">
    <table class="ui very basic table" style="width:100%;">
        <tbody>
            <tr>
                <td class="font-bold w-1/3 text-right pr-2">Customer Name</td>
                <td class="text-center w-[10px]">:</td>
                <td>{{ $data->customer_name }}</td>
            </tr>
            <tr>
                <td class="font-bold text-right pr-2">Sales Order Number (SO)</td>
                <td class="text-center w-[10px]">:</td>
                <td>{{ $data->so_number ?? '-- Not Input Yet --' }}</td>
            </tr>
            <tr>
                <td class="font-bold text-right pr-2">Purchase Order Number (PO)</td>
                <td class="text-center w-[10px]">:</td>
                <td>{{ $data->po_number }}</td>
            </tr>
            <tr>
                <td class="font-bold text-right pr-2">Description</td>
                <td class="text-center w-[10px]">:</td>
                <td style="white-space: pre-line;">{{ $data->description }}</td>
            </tr>
            <tr>
                <td class="font-bold text-right pr-2">Status</td>
                <td class="text-center w-[10px]">:</td>
                <td>
                    @if ($data->as_additional_data)
                        <div class='flex gap-3'>
                            <span class='ui green label'>Additional Data</span> 
                            <span class="ui label {{ $renderStatusColor($data->status) }}">
                                {{ $data->status }}
                            </span>
                        </div>
                    @else
                        <span class="ui label {{ $renderStatusColor($data->status) }}">
                            {{ $data->status }}
                        </span>
                    @endif
                </td>
            </tr>
            @if ($data->as_additional_data)
                <tr>
                    <td class="font-bold text-right pr-2">Additional Data Note</td>
                    <td class="text-center w-[10px]">:</td>
                    <td style="white-space: pre-line;">{{ $data->additional_data_note }}</td>
                </tr>
            @endif  
            @if ($data->need_revise_note && $data->status === StatusDrawingTransaction::REVISE_NEEDED->value)
                <tr>
                    <td class="font-bold text-right pr-2">Need Revise Note</td>
                    <td class="text-center w-[10px]">:</td>
                    <td style="white-space: pre-line;">{{ $data->need_revise_note }}</td>
                </tr>
            @endif
            <tr>
                <td class="font-bold text-right pr-2">Created At</td>
                <td class="text-center w-[10px]">:</td>
                <td style="white-space: pre-line;">{{ Carbon::parse($data->created_at)->format('d M Y H:i:s') }}</td>
            </tr>
            <tr>
                <td class="font-bold text-right pr-2">Distributed At</td>
                <td class="text-center w-[10px]">:</td>
                <td style="white-space: pre-line;">{{ isset($data->distributed_at) ? Carbon::parse($data->distributed_at)->format('d M Y H:i:s') : '-- Not Distribute Yet --' }}</td>
            </tr>
            <tr>
                <td class="font-bold text-right pr-2">Uploaded Files</td>
                <td class="text-center w-[10px]">:</td>
                <td>
                    <div id="previewContainer" class="flex flex-wrap gap-3 mt-2" style="min-height:150px;"></div>
                </td>
            </tr>
        </tbody>
    </table>
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
