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

                    <!-- SINGLE hidden input -->
                    <input type="file" name="file" id="fileInput" accept="image/*" hidden>

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

            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('previewContainer');

            // Open camera
            window.openCamera = function () {
                fileInput.setAttribute('capture', 'environment'); // force camera
                fileInput.click();
            };

            // Open gallery
            window.openGallery = function () {
                fileInput.removeAttribute('capture'); // allow gallery
                fileInput.click();
            };

            // Handle file selection
            fileInput.addEventListener('change', function (e) {
                const file = e.target.files[0];

                if (!file || !file.type.startsWith('image/')) return;

                // Clear previous preview (only 1 file allowed)
                previewContainer.innerHTML = '';

                const imageUrl = URL.createObjectURL(file);

                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                wrapper.style.width = '120px';
                wrapper.style.height = '150px';

                const img = document.createElement('img');
                img.src = imageUrl;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '6px';

                wrapper.appendChild(img);

                // Click → open full image
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
                    fileInput.value = ''; // clear selected file
                };

                wrapper.appendChild(btn);
                previewContainer.appendChild(wrapper);
            });

            // Dropdown logic
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
