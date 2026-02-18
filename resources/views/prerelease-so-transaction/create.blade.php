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
    @include('shared.appbar', ['backRoute' => 'prereleaseSoTransactionView', 'title' => 'Create New Prerelease So Transaction'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form id="createForm" class="ui form" method="post" action="{{ route('prereleaseSoTransactionCreate') }}" enctype="multipart/form-data">
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
                        <input type="hidden" name="customer">
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
                        <input type="hidden" name="target_shipment">
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
                        <input type="hidden" name="area">
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
                    <label class="!text-base"">Sales Order Number (SO)</label>
                    <input type="text" name="so_number" placeholder="Sales Order Number">
                </div>
                <div class="field flex-1">
                    <label class="!text-base"">Purchase Order Number (PO)</label>
                    <input type="text" name="po_number" placeholder="Purchase Order Number">
                </div>
                <div class="field">
                    <label class="!text-base"">Description</label>
                    <textarea style="resize: none;" name="description" placeholder="Description" rows="3"></textarea>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" tabindex="0" class="hidden" name="as_revision_data">
                        <label class="!text-base font-bold">As Revision Data</label>
                    </div>
                </div>
                <div class="field hidden" id="revisionDataNoteWrapper">
                    <label class="!text-base"">Revision Data Note</label>
                    <textarea style="resize: none;" name="revision_data_note" placeholder="Revision Data Note" rows="3"></textarea>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" tabindex="0" class="hidden" name="as_additional_data">
                        <label class="!text-base font-bold">As Additional Data</label>
                    </div>
                </div>
                <div class="field hidden" id="additionalDataNoteWrapper">
                    <label class="!text-base"">Additional Data Note</label>
                    <textarea style="resize: none;" name="additional_data_note" placeholder="Additional Data Note" rows="3"></textarea>
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
        document.addEventListener('DOMContentLoaded', function() {

            const asAdditionalData = $('[name="as_additional_data"]');
            const additionalDataNote = $('#additionalDataNoteWrapper');

            asAdditionalData.on('change', function () {
                if (asAdditionalData.is(':checked')) {
                    additionalDataNote.removeClass('hidden');
                } else {
                    additionalDataNote.addClass('hidden');
                }
            });

            const asRevisionData = $('[name="as_revision_data"]');
            const revisionDataNote = $('#revisionDataNoteWrapper');

            asRevisionData.on('change', function () {
                if (asRevisionData.is(':checked')) {
                    revisionDataNote.removeClass('hidden');
                } else {
                    revisionDataNote.addClass('hidden');
                }
            });

            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('previewContainer');
            fileInput._files = fileInput._files || [];

            fileInput.addEventListener('change', async function(event) {
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
        });

        $('.ui.checkbox').checkbox();

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

            const form = $('#createForm');
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
                hiddenInput.val(0);
                form[0].submit();
            });
        });
    </script>
@endsection