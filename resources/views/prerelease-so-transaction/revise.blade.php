@extends('master.layout')

@section('content')
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
    @include('shared.appbar', ['backRoute' => 'prereleaseSoTransactionView', 'title' => 'Revise Prerelease So Transaction'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form id="reviseForm" class="ui form" method="post" action="{{ route('prereleaseSoTransactionRevise', $data->id) }}" enctype="multipart/form-data">
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
                
                <div class="field flex-1">
                     <label class="!text-base">Target Shipment</label>
                    <div id="targetShipmentsDropdown" class="ui clearable selection dropdown">
                        <input type="hidden" name="target_shipment" value="{{ sprintf('%04d-%02d', $data->target_shipment_year, $data->target_shipment_month) }}">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select Target Shipment</div>
                        <div class="menu">
                            @foreach ($months as $month)
                                <div class="item" data-value="{{ $month['value'] }}">{{ $month['label'] }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- <div class="field flex-1">
                     <label class="!text-base">Area</label>
                    <div id="areasDropdown" class="ui clearable selection dropdown">
                        <input type="hidden" name="area" value="{{ $data->area->id }}">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select Area</div>
                        <div class="menu">
                            @foreach ($areas as $area)
                                <div class="item" data-value="{{ $area->id }}">{{ $area->name }}</div>
                            @endforeach
                        </div>
                    </div>
                </div> --}}

                <div class="field flex-1">
                    <label class="!text-base"">Sales Order Number (PO)</label>
                    <input type="text" name="so_number" placeholder="Sales Order Number" value="{{ $data->so_number }}">
                </div>
                <div class="field flex-1">
                    <label class="!text-base"">Purchase Order Number (PO)</label>
                    <input type="text" name="po_number" placeholder="Purchase Order Number" value="{{ $data->po_number }}">
                </div>
                <div class="field">
                    <label class="!text-base"">Description</label>
                    <textarea style="resize: none;" name="description" placeholder="Description" rows="3">{{ $data->description }}</textarea>
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
                
                <input type="hidden" name="is_urgent" id="isUrgentConfirmationAction">

                <button class="ui button customButton" id="submitBtn">Submit</button>
            </form>

            <div class="ui small modal !w-[350px]" id="isUrgentConfirmModal">
                <div class="header !text-base">Confirm Urgency</div>
                <div class="content">
                    <p>Is this transaction considered urgent?</p>
                </div>
                <div class="actions">
                    <div class="ui red button" id="noBtn">No</div>
                    <div class="ui green button" id="yesBtn">Yes</div>
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
        document.addEventListener('DOMContentLoaded', async function() {
            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('previewContainer');
            fileInput._files = fileInput._files || [];

            // --- STEP 1: Load previous files from server ---
            // const previousFiles = @json($data->filepath ? [$data->filepath] : []);
            // for (let i = 0; i < previousFiles.length; i++) {
            //     const fileUrl = "/storage/" + previousFiles[i];

            //     const typedArray = await fetch(fileUrl)
            //         .then(res => res.arrayBuffer())
            //         .then(buffer => new Uint8Array(buffer));

            //     const pdf = await pdfjsLib.getDocument(typedArray).promise;
            //     const page = await pdf.getPage(1);

            //     const fixedWidth = 120;
            //     const fixedHeight = 150;
            //     const viewport = page.getViewport({ scale: 1 });
            //     const scale = Math.min(fixedWidth / viewport.width, fixedHeight / viewport.height);
            //     const scaledViewport = page.getViewport({ scale });

            //     const canvas = document.createElement('canvas');
            //     canvas.width = fixedWidth;
            //     canvas.height = fixedHeight;

            //     const context = canvas.getContext('2d');
            //     context.fillStyle = '#fff';
            //     context.fillRect(0, 0, fixedWidth, fixedHeight);

            //     const offsetX = (fixedWidth - scaledViewport.width) / 2;
            //     const offsetY = (fixedHeight - scaledViewport.height) / 2;

            //     await page.render({
            //         canvasContext: context,
            //         viewport: scaledViewport,
            //         transform: [1, 0, 0, 1, offsetX, offsetY]
            //     }).promise;

            //     const wrapper = document.createElement('div');
            //     wrapper.className = 'pdf-wrapper';
            //     wrapper.style.width = fixedWidth + 'px';
            //     wrapper.style.height = fixedHeight + 'px';
            //     wrapper.dataset.previous = fileUrl; // mark as previous
            //     wrapper.appendChild(canvas);

            //     // click opens PDF
            //     wrapper.addEventListener('click', () => window.open(fileUrl, '_blank'));

            //     // remove button
            //     const btn = document.createElement('button');
            //     btn.type = 'button';
            //     btn.className = 'ui red mini circular icon button';
            //     btn.style.position = 'absolute';
            //     btn.style.top = '5px';
            //     btn.style.right = '5px';
            //     btn.style.opacity = '0.85';
            //     btn.innerHTML = '<i class="close icon"></i>';
            //     btn.addEventListener('click', (e) => {
            //         e.stopPropagation();
            //         wrapper.remove();
            //     });

            //     wrapper.appendChild(btn);
            //     previewContainer.appendChild(wrapper);
            // }

            // --- STEP 2: Handle new uploads ---
            fileInput.addEventListener('change', async function(event) {
                const newFiles = Array.from(event.target.files);

                // Remove previous previews
                previewContainer.querySelectorAll('.pdf-wrapper').forEach(w => {
                    if (!w.dataset.previous) w.remove();
                });

                for (let file of newFiles) {
                    const index = fileInput._files.length;
                    fileInput._files.push(file);

                    const reader = new FileReader();
                    reader.onload = async function(e) {
                        const typedArray = new Uint8Array(e.target.result);
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
                        wrapper.style.position = 'relative';
                        wrapper.style.width = fixedWidth + 'px';
                        wrapper.style.height = fixedHeight + 'px';
                        wrapper.style.flexShrink = '0';
                        wrapper.className = 'pdf-wrapper';
                        wrapper.appendChild(canvas);

                        // open in new tab
                        wrapper.addEventListener('click', (e) => {
                            if (e.target.tagName.toLowerCase() === 'i') return;
                            const pdfUrl = URL.createObjectURL(fileInput._files[index]);
                            window.open(pdfUrl, '_blank');
                        });

                        // remove button
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'ui red mini circular icon button';
                        btn.style.position = 'absolute';
                        btn.style.top = '5px';
                        btn.style.right = '5px';
                        btn.style.opacity = '0.85';
                        btn.innerHTML = '<i class="close icon"></i>';
                        btn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            wrapper.remove();
                            fileInput._files[index] = null;

                            // rebuild input files
                            const dataTransfer = new DataTransfer();
                            fileInput._files.forEach(f => { if(f) dataTransfer.items.add(f); });
                            fileInput.files = dataTransfer.files;

                            // if all new files removed, show previous file
                            if (!fileInput.files.length) {
                                previewContainer.querySelectorAll('.pdf-wrapper[data-previous]').forEach(w => w.style.display = 'flex');
                            }
                        });

                        wrapper.appendChild(btn);
                        previewContainer.appendChild(wrapper);

                        // hide previous file preview
                        previewContainer.querySelectorAll('.pdf-wrapper[data-previous]').forEach(w => w.style.display = 'none');
                    };
                    reader.readAsArrayBuffer(file);
                }

                // rebuild input files
                const dataTransfer = new DataTransfer();
                fileInput._files.forEach(f => { if(f) dataTransfer.items.add(f); });
                fileInput.files = dataTransfer.files;
            });
        });

        $(document).ready(function() {
            $('#customersDropdown').dropdown();
            // $('#areasDropdown').dropdown();

            const targetDropdown = $('#targetShipmentsDropdown');
            targetDropdown.dropdown();

            const today = new Date().getDate(); // current day (1–31)

            // If day > 11 → take second item, else first item
            let defaultItem;

            if (today > 11) {
                defaultItem = targetDropdown.find('.menu .item').eq(1); // second item
            } else {
                defaultItem = targetDropdown.find('.menu .item').eq(0); // first item
            }

            if (defaultItem.length) {
                const value = defaultItem.data('value');
                targetDropdown.dropdown('set selected', value);
            }

            const form = $('#reviseForm');
            const submitBtn = $('#submitBtn');
            const modal = $('#isUrgentConfirmModal');
            const hiddenInput = $('#isUrgentConfirmationAction');

            submitBtn.on('click', function (e) {
                e.preventDefault();

                const today = new Date().getDate();

                const targetDropdown = $('#targetShipmentsDropdown');

                // Get currently selected value
                const selectedValue = targetDropdown.dropdown('get value');

                // Get first item's value dynamically
                const firstItemValue = targetDropdown
                    .find('.menu .item')
                    .eq(0)
                    .data('value');

                const isFirstItem = selectedValue == firstItemValue;

                if (today > 11 && isFirstItem) {
                    // Show confirmation modal
                    modal.modal('show');
                } else {
                    // Submit directly
                    hiddenInput.val(0);
                    form[0].submit();
                }
            });

            $('#yesBtn').on('click', function () {
                hiddenInput.val(1);
                form[0].submit();
            });

            $('#noBtn').on('click', function () {
                modal.modal('hide');
            });
        });
    </script>
@endsection