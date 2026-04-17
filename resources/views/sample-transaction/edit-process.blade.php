@extends('master.layout')

@section('content')
    <style>
        .hover-preview {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
        }

        .hover-preview:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .hover-preview button {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .hover-preview:hover button {
            opacity: 1;
        }
    </style>
    @include('shared.appbar', ['backRoute' => 'sampleTransactionView', 'title' => 'Edit Sample Transaction Process'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('sampleTransactionEditProcess', $data->id) }}" enctype="multipart/form-data">
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
                            {{ $data->sample->so_number }}
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="font-bold mb-1">Process</div>
                    <div class="ui input w-full !cursor-default opacity-70">
                        <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                            {{ $data->process_name }}
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="!text-base">Picture</label>

                    <div class="ui">
                        <input class="ui invisible file input" type="file" name="file" id="fileInput" accept="image/*">
                        <label for="fileInput" class="ui icon button">
                            <i class="file icon"></i>
                            Upload Picture
                        </label>
                    </div>
                    <!-- Preview container -->
                    <div id="previewContainer" class="ui small images" 
                        style="margin-top:15px; padding: 5px; display:flex; gap:10px; flex-wrap:wrap;">
                    </div>
                </div>

                <input type="hidden" name="existing_file" value="{{ $data->filepath }}">

                <div class="field">
                    <label class="!text-base">Finish Note</label>
                    <textarea style="resize: none;" name="finish_note" placeholder="Finish Note" rows="3">{{ $data->finish_note }}</textarea>
                </div>

                <button class="ui button customButton mt-4" type="submit">Finish</button>
            </form>
        </div>
    </div>

    <script>
        const existingFile = "{{ $data->filepath ? asset('storage/' . $data->filepath) : '' }}";
    </script>

    <script>
        $(document).ready(function () {
            
            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('previewContainer');

            fileInput._files = fileInput._files || [];

            function renderExisting(fileUrl) {
                if (!fileUrl) return;

                const wrapper = document.createElement('div');
                wrapper.classList.add('hover-preview', 'existing-file');

                wrapper.style.position = 'relative';
                wrapper.style.width = '120px';
                wrapper.style.height = '150px';
                wrapper.style.flexShrink = '0';

                const img = document.createElement('img');
                img.src = fileUrl;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '6px';

                wrapper.appendChild(img);

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'existing_file';
                hidden.value = fileUrl;
                wrapper.appendChild(hidden);

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
                });

                wrapper.addEventListener('click', () => {
                    window.open(fileUrl, '_blank');
                });

                wrapper.appendChild(btn);
                previewContainer.appendChild(wrapper);
            }

            renderExisting(existingFile);

            fileInput.addEventListener('change', function(event) {
                // Remove the existing file display
                document.querySelectorAll('.existing-file').forEach(el => el.remove());
                document.querySelector('input[name="existing_file"]').value = '';

                const newFiles = Array.from(event.target.files);

                for (let file of newFiles) {
                    const index = fileInput._files.length;
                    fileInput._files.push(file);

                    // Only process image
                    if (!file.type.startsWith('image/')) continue;

                    const imageUrl = URL.createObjectURL(file);

                    const wrapper = document.createElement('div');
                    wrapper.classList.add('hover-preview');

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
                    wrapper.addEventListener('click', (e) => {
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
                    btn.style.opacity = '0.85';
                    btn.innerHTML = '<i class="close icon"></i>';

                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        wrapper.remove();
                        fileInput._files[index] = null;

                        const dataTransfer = new DataTransfer();
                        fileInput._files.forEach(f => { if (f) dataTransfer.items.add(f); });
                        fileInput.files = dataTransfer.files;
                    });

                    wrapper.appendChild(btn);
                    previewContainer.appendChild(wrapper);
                }

                // Rebuild input files
                const dataTransfer = new DataTransfer();
                fileInput._files.forEach(f => { if (f) dataTransfer.items.add(f); });
                fileInput.files = dataTransfer.files;
            });

            $('#processesDropdown').dropdown();

            const startAt = $('#start-at');
            const finishAt = $('#finish-at');

            startAt.calendar({
                type: 'datetime',
                endCalendar: finishAt
            });

            finishAt.calendar({
                type: 'datetime',
                startCalendar: startAt
            });

        });
    </script>
@endsection
