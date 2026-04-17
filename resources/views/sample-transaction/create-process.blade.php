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

                <div class="field">
                    <label class="!text-base">Start Note</label>
                    <textarea style="resize: none;" name="start_note" placeholder="Start Note" rows="3"></textarea>
                </div>

                <button class="ui button customButton mt-4" type="submit">Start</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('previewContainer');

            fileInput._files = fileInput._files || [];

            fileInput.addEventListener('change', function(event) {
                
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
        });
    </script>
@endsection
