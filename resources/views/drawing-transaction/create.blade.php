@extends('master.layout')

@section('content')
    @include('shared.back-button', ['backRoute' => 'drawingTransactionView'])
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

            <div class="field">
                <label class="!text-base"">Customer Name</label>
                <input type="text" name="customer_name" placeholder="Customer Name">
            </div>
            <div class="flex flex-row gap-5">
                <div class="field flex-1">
                    <label class="!text-base"">Sales Order Number (SO)</label>
                    <input type="text" name="so_number" placeholder="Sales Order Number">
                </div>
                <div class="field flex-1">
                    <label class="!text-base"">Purchase Order Number (PO)</label>
                    <input type="text" name="po_number" placeholder="Purchase Order Number">
                </div>
            </div>
            <div class="field">
                <label class="!text-base"">Description</label>
                <textarea style="resize: none;" name="description" placeholder="Description"></textarea>
            </div>
                <div class="field">
                    <label class="!text-base">Upload Images</label>

                    <div class="ui file input">
                        <input type="file" name="files[]" multiple id="fileInput" accept="image/*">
                    </div>
                    <!-- Preview container -->
                    <div id="previewContainer" class="ui small images" 
                        style="margin-top:15px; padding: 5px; display:flex; gap:10px; flex-wrap:wrap;">
                    </div>
                </div>
            <button class="ui button customButton" type="submit">Create</button>
        </form>
    </div>

<script>
const fileInput = $('#fileInput')[0];
if (!fileInput._files) fileInput._files = [];

// Add files
$('#fileInput').on('change', function(event) {
    const newFiles = Array.from(event.target.files);
    if (!newFiles.length) return;

    newFiles.forEach(file => {
        const index = fileInput._files.length;
        fileInput._files.push(file);

        if (!file.type.startsWith("image/")) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const html = `
                <div data-index="${index}" 
                    style="position:relative; width:120px; height:120px; flex-shrink:0; 
                           display:flex; align-items:center; justify-content:center; border:1px solid #ddd;">
                    <img src="${e.target.result}" 
                        style="max-width:100%; max-height:100%; object-fit:contain; border-radius:0;">
                    <button class="ui red mini circular icon button remove-btn"
                            style="position:absolute; top:5px; right:5px; opacity:0.85;">
                        <i class="close icon"></i>
                    </button>
                </div>
            `;
            $('#previewContainer').append(html);
        };
        reader.readAsDataURL(file);
    });

    // Update input.files
    const dataTransfer = new DataTransfer();
    fileInput._files.forEach(f => dataTransfer.items.add(f));
    fileInput.files = dataTransfer.files;
});

// Remove preview + file
$(document).on('click', '.remove-btn', function() {
    const parent = $(this).closest('[data-index]');
    const index = parseInt(parent.attr('data-index'));

    // Remove preview
    parent.remove();

    // Remove file from _files
    fileInput._files[index] = null; // mark as removed

    // Rebuild input.files without nulls
    const dataTransfer = new DataTransfer();
    fileInput._files.forEach(f => {
        if (f) dataTransfer.items.add(f);
    });
    fileInput.files = dataTransfer.files;

    // Keep _files array same length to preserve index mapping
});

</script>


@endsection