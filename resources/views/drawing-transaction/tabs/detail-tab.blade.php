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
<div>
    <form class="ui form" method="post" action="{{ route('drawingTransactionCreate') }}" enctype="multipart/form-data">
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

        <div class="flex flex-row gap-5">
            <div class="field flex-1">
                <label class="!text-base"">Customer Name</label>
                <input type="text" name="customer_name" placeholder="Customer Name" value="{{ $data->customer_name }}" disabled>
            </div>
            <div class="field flex-1">
                <label class="!text-base"">Purchase Order Number (PO)</label>
                <input type="text" name="po_number" placeholder="Purchase Order Number" value="{{ $data->po_number }}" disabled>
            </div>
        </div>
        <div class="field">
            <label class="!text-base"">Description</label>
            <textarea style="resize: none;" name="description" placeholder="Description" disabled>{{ $data->description }}</textarea>
        </div>
        <div class="field">
            <label class="!text-base">Uploaded Files</label>

            <!-- Preview container -->
            <div id="previewContainer" class="ui small images" 
                style="margin-top:15px; padding: 5px; display:flex; gap:10px; flex-wrap:wrap;">
            </div>
        </div>
    </form>
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
        // if it's not an array, convert it to array
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
