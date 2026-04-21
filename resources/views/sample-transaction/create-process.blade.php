@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'sampleTransactionView', 'title' => 'Start New Sample Transaction Process'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('sampleTransactionCreateProcess', $sampleTransaction->id) }}" enctype="multipart/form-data">
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
                            {{ $sampleTransaction->so_number }}
                        </div>
                    </div>
                </div>

                <div class="field flex-1">
                     <label class="!text-base">Process</label>
                    <div id="processesDropdown" class="ui clearable selection dropdown">
                        <input type="hidden" name="process">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select Process</div>
                        <div class="menu">
                            @foreach ($processes as $process)
                                <div class="item" data-value="{{ $process }}">{{ $process }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="!text-base">Picture</label>

                    <!-- Buttons -->
                    <div class="ui buttons">
                        <button type="button" class="ui icon button" onclick="openCamera()">
                            <i class="camera icon"></i>
                            Camera
                        </button>
                        <div class="or"></div>
                        <button type="button" class="ui icon button" onclick="openGallery()">
                            <i class="folder open icon"></i>
                            Gallery
                        </button>
                    </div>

                    <!-- Hidden inputs -->
                    <input type="file" id="cameraInput" accept="image/*" capture="environment" hidden>
                    <input type="file" id="galleryInput" accept="image/*" hidden>

                    <!-- Preview -->
                    <div id="previewContainer" class="ui small images"
                        style="margin-top:15px; padding:5px; display:flex; gap:10px; flex-wrap:wrap;">
                    </div>
                </div>

                <div class="field">
                    <label id="startNoteLabel" class="!text-base">Start Note</label>
                    <textarea style="resize: none;" name="start_note" placeholder="Start Note" rows="3"></textarea>
                </div>

                <button id="submitBtn" class="ui button customButton mt-4" type="submit">Start</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            const cameraInput = document.getElementById('cameraInput');
            const galleryInput = document.getElementById('galleryInput');
            const previewContainer = document.getElementById('previewContainer');

            let allFiles = [];

            function handleFiles(files) {
                for (let file of files) {
                    const index = allFiles.length;
                    allFiles.push(file);

                    if (!file.type.startsWith('image/')) continue;

                    const imageUrl = URL.createObjectURL(file);

                    const wrapper = document.createElement('div');
                    wrapper.style.position = 'relative';
                    wrapper.style.width = '120px';
                    wrapper.style.height = '150px';
                    wrapper.style.flexShrink = '0';

                    const img = document.createElement('img');
                    img.src = imageUrl;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '6px';

                    wrapper.appendChild(img);

                    // Click → open image
                    wrapper.addEventListener('click', function (e) {
                        if (e.target.tagName.toLowerCase() === 'i') return;
                        window.open(imageUrl, '_blank');
                    });

                    // Remove button
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'ui red mini circular icon button';
                    btn.style.position = 'absolute';
                    btn.style.top = '5px';
                    btn.style.right = '5px';
                    btn.innerHTML = '<i class="close icon"></i>';

                    btn.onclick = function (e) {
                        e.stopPropagation();
                        wrapper.remove();
                        allFiles[index] = null;
                    };

                    wrapper.appendChild(btn);
                    previewContainer.appendChild(wrapper);
                }
            }

            // Handle camera
            cameraInput.addEventListener('change', function (e) {
                handleFiles(Array.from(e.target.files));
                cameraInput.value = ''; // reset
            });

            // Handle gallery
            galleryInput.addEventListener('change', function (e) {
                handleFiles(Array.from(e.target.files));
                galleryInput.value = ''; // reset
            });

            // Button triggers
            window.openCamera = function () {
                cameraInput.click();
            };

            window.openGallery = function () {
                galleryInput.click();
            };

            // Before submit → merge all files
            $('form').on('submit', function () {
                const dataTransfer = new DataTransfer();

                allFiles.forEach(f => {
                    if (f) dataTransfer.items.add(f);
                });

                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'file[]';
                input.multiple = true;
                input.files = dataTransfer.files;

                this.appendChild(input);
            });

            // Dropdown (unchanged)
            $('#processesDropdown').dropdown({
                onChange: function (value) {
                    if (value === 'Finish Good') {
                        submitBtn.innerText = 'Finish';
                        startNoteLabel.innerHTML = 'Finish Note';
                    } else {
                        submitBtn.innerText = 'Start';
                        startNoteLabel.innerHTML = 'Start Note';
                    }
                }
            });

        });
    </script>
@endsection
