@extends('master.layout')

@section('content')
    <style>
        /* Remove tab borders & background */
        .ui.tabular.menu .item {
            border: none !important;
            background: transparent !important;
        }

        /* Remove active item underline and background */
        .ui.tabular.menu .item.active {
            border: none !important;
            background: transparent !important;
            color: var(--primary-color) !important;
        }

        /* Remove bottom border line under menu */
        .ui.tabular.menu {
            border-bottom: none !important;
            box-shadow: none !important;
        }

        /* Remove segment box style */
        .ui.tab.segment {
            border: none !important;
            background: transparent !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    </style>

    <div class="flex items-center gap-3">
        @include('shared.appbar', ["backRoute" => 'drawingTransactionView', 'title' => 'Detail Drawing Transaction'])
        @if ($data->as_additional_data)
            <div class="mb-10 flex justify-end">
                <span class='ui large green label'>Additional Data</span>
            </div>
        @endif
    </div>

    <div class="ui top attached menu !mb-8 !border-b">
        <a class="!text-lg item !font-bold" data-tab="detail">Detail</a>
        <a class="!text-lg item !font-bold" data-tab="approval">Approval / Rejection</a>
        <a class="!text-lg item !font-bold" data-tab="steps">Activity History</a>
    </div>

    <div class="ui attached tab segment" data-tab="detail" style="overflow: visible;">
        @include('drawing-transaction.tabs.detail-tab', ['data' => $data])
    </div>

    <div class="ui attached tab segment" data-tab="approval" style="overflow: visible;">
        @include('drawing-transaction.tabs.approval-tab')
    </div>

    <div class="ui attached tab segment" data-tab="steps" id="stepsTab" style="overflow: visible;">
        <div class="ui active inverted dimmer !bg-transparent" id="stepsLoader">
            <div class="ui loader"></div>
        </div>
    </div>

    <script>

        const tabItems = document.querySelectorAll(".menu .item");

        // Get last saved tab OR default
        let activeTab = localStorage.getItem("active_tab") || "detail";

        // Remove all active first
        document.querySelectorAll(".menu .item").forEach(i => i.classList.remove("active"));
        document.querySelectorAll(".tab.segment").forEach(i => i.classList.remove("active"));

        // Add active class to saved tab
        document.querySelector(`.menu .item[data-tab="${activeTab}"]`)?.classList.add("active");
        document.querySelector(`.tab.segment[data-tab="${activeTab}"]`)?.classList.add("active");

        // Save the clicked tab
        tabItems.forEach(item => {
            item.addEventListener("click", () => {
                const tab = item.getAttribute("data-tab");
                localStorage.setItem("active_tab", tab);
            });
        });

        $('.menu .item').tab({
            onVisible: function (tabName) {
                if (tabName === 'steps') loadStepsTab();
            }
        });

        let stepsTabLoaded = false;

        if (activeTab === "steps") {
            loadStepsTab();
        }

        function loadStepsTab() {
            if (stepsTabLoaded) return;
            $("#stepsLoader").addClass("active");

            $.get("{{ route('drawingTransactionSteps', $data->id) }}", function (html) {
                $("#stepsTab").html(html);
                stepsTabLoaded = true; // mark as loaded
            }).always(() => {
                $("#stepsLoader").removeClass("active");
            });
        }

        async function initStepPreview(rejectedFilesData) {

            for (const dt of rejectedFilesData) {
                if (!dt.filepath) continue;

                const container = document.getElementById('previewContainer_' + dt.id);
                if (!container) continue;

                const fileUrl = "/storage/" + dt.filepath;

                const typedArray = await fetch(fileUrl)
                    .then(res => res.arrayBuffer())
                    .then(buffer => new Uint8Array(buffer));

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

                wrapper.addEventListener('click', () => window.open(fileUrl, '_blank'));
                container.appendChild(wrapper);
            }
        }
    </script>
@endsection
