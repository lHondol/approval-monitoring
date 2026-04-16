@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'sampleTransactionView', 'title' => 'Edit Sample Transaction'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('sampleTransactionEdit', $data->id) }}" enctype="multipart/form-data">
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
                            {{ $data->so_number }}
                        </div>
                    </div>
                </div>

                <div class="field flex-1">
                     <label class="!text-base">Customer</label>
                    <div id="customersDropdown" class="ui clearable selection dropdown">
                        <input type="hidden" name="customer" value="{{ $data->customer->id }}">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select Customer</div>
                        <div class="menu">
                            @foreach ($customers as $customer)
                                <div class="item" data-value="{{ $customer->id }}">{{ $customer->name }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="!text-base">SO Created At</label>
                    <div id="so-created-at" class="ui calendar">
                        <div class="ui input left icon">
                            <i class="calendar icon"></i>
                            <input type="text" name="so_created_at" placeholder="YYYY-MM-DD HH:MM AM" value="{{ $data->so_created_at }}">
                        </div>
                    </div>
                </div>

                
                <div class="field">
                    <label class="!text-base">Shipment Request</label>
                    <div id="shipment-request" class="ui calendar">
                        <div class="ui input left icon">
                            <i class="calendar icon"></i>
                            <input type="text" name="shipment_request" placeholder="YYYY-MM-DD HH:MM AM" value="{{ $data->shipment_request }}">
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="!text-base">Note</label>
                    <textarea style="resize: none;" name="note" placeholder="Sales Order Note" rows="3">{{ $data->note }}</textarea>
                </div>

                <div class="field">
                    <label class="!text-base">Upload Files</label>

                    <div class="ui">
                        <input class="ui invisible file input" type="file" name="files[]" multiple id="fileInput" accept="application/pdf">
                        <label for="fileInput" class="ui icon button">
                            <i class="file icon"></i>
                            Upload PDF File
                        </label>
                    </div>
                    <!-- Preview container -->
                    <div id="previewContainer" class="ui small images" 
                        style="margin-top:15px; padding: 5px; display:flex; gap:10px; flex-wrap:wrap;">
                    </div>
                </div>

                <input type="hidden" name="existing_file" value="{{ $data->filepath }}">

                <button class="ui button customButton mt-4" type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        const existingFile = "{{ $data->filepath ? asset('storage/' . $data->filepath) : '' }}";
    </script>

    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    </script>


    <script>        
        $(document).ready(function() {
            async function renderExistingPDF(fileUrl) {
                if (!fileUrl) return;

                const loadingTask = pdfjsLib.getDocument(fileUrl);
                const pdf = await loadingTask.promise;
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
                wrapper.style.position = 'relative';
                wrapper.style.width = fixedWidth + 'px';
                wrapper.style.height = fixedHeight + 'px';
                wrapper.style.flexShrink = '0';
                wrapper.className = 'pdf-wrapper existing-file';

                wrapper.appendChild(canvas);

                // open PDF
                wrapper.addEventListener('click', (e) => {
                    if (e.target.tagName.toLowerCase() === 'i') return;
                    window.open(fileUrl, '_blank');
                });

                // remove button
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'ui red mini circular icon button';
                btn.style.position = 'absolute';
                btn.style.top = '5px';
                btn.style.right = '5px';
                btn.innerHTML = '<i class="close icon"></i>';

                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    wrapper.remove();
                });

                wrapper.appendChild(btn);
                previewContainer.appendChild(wrapper);
            }

            if (existingFile) {
                renderExistingPDF(existingFile);
            }

            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('previewContainer');
            fileInput._files = fileInput._files || [];

            fileInput.addEventListener('change', async function(event) {
                // Remove the existing file display
                document.querySelectorAll('.existing-file').forEach(el => el.remove());
                document.querySelector('input[name="existing_file"]').value = '';

                const newFiles = Array.from(event.target.files);

                for (let file of newFiles) {
                    const index = fileInput._files.length;
                    fileInput._files.push(file);

                    const reader = new FileReader();
                    reader.onload = async function(e) {
                        const typedArray = new Uint8Array(e.target.result);
                        const pdf = await pdfjsLib.getDocument(typedArray).promise;
                        const page = await pdf.getPage(1);

                        // Fixed preview size
                        const fixedWidth = 120;
                        const fixedHeight = 150;
                        const viewport = page.getViewport({ scale: 1 });
                        const scale = Math.min(fixedWidth / viewport.width, fixedHeight / viewport.height);
                        const scaledViewport = page.getViewport({ scale });

                        const canvas = document.createElement('canvas');
                        canvas.width = fixedWidth;
                        canvas.height = fixedHeight;
                        const context = canvas.getContext('2d');
                        context.fillStyle = '#fff'; // white background
                        context.fillRect(0, 0, fixedWidth, fixedHeight);

                        // Center the PDF page in canvas
                        const offsetX = (fixedWidth - scaledViewport.width) / 2;
                        const offsetY = (fixedHeight - scaledViewport.height) / 2;

                        await page.render({
                            canvasContext: context,
                            viewport: scaledViewport,
                            transform: [1, 0, 0, 1, offsetX, offsetY] // translate
                        }).promise;

                        const wrapper = document.createElement('div');
                        wrapper.style.position = 'relative';
                        wrapper.style.width = fixedWidth + 'px';
                        wrapper.style.height = fixedHeight + 'px';
                        wrapper.style.flexShrink = '0';
                        wrapper.className = 'pdf-wrapper';
                        wrapper.appendChild(canvas);

                        // Open in new tab on click
                        wrapper.addEventListener('click', (e) => {
                            // Avoid triggering when clicking remove button
                            if (e.target.tagName.toLowerCase() === 'i') return;
                            const pdfUrl = URL.createObjectURL(fileInput._files[index]);
                            window.open(pdfUrl, '_blank');
                        });

                        // Remove button with Semantic UI icon
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'ui red mini circular icon button';
                        btn.style.position = 'absolute';
                        btn.style.top = '5px';
                        btn.style.right = '5px';
                        btn.style.opacity = '0.85';
                        btn.innerHTML = '<i class="close icon"></i>';
                        btn.addEventListener('click', (e) => {
                            e.stopPropagation(); // prevent opening PDF
                            wrapper.remove();
                            fileInput._files[index] = null;

                            const dataTransfer = new DataTransfer();
                            fileInput._files.forEach(f => { if(f) dataTransfer.items.add(f); });
                            fileInput.files = dataTransfer.files;
                        });

                        wrapper.appendChild(btn);
                        previewContainer.appendChild(wrapper);
                    };
                    reader.readAsArrayBuffer(file);
                }

                // Rebuild input files
                const dataTransfer = new DataTransfer();
                fileInput._files.forEach(f => { if(f) dataTransfer.items.add(f); });
                fileInput.files = dataTransfer.files;
            });
            
            $('#customersDropdown').dropdown();

            const soCreatedAt = $('#so-created-at');
            const shipmentRequest = $('#shipment-request');

            soCreatedAt.calendar({
                type: 'datetime'
            });

            shipmentRequest.calendar({
                type: 'datetime'
            });
        });
    </script>
@endsection
