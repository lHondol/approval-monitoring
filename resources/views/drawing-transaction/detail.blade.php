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
        /* Remove tab borders & background */
        .ui.tabular.menu .item {
            border: none !important;
            background: transparent !important;
        }

        /* Remove active item underline and background */
        .ui.menu .item.active button.ui.button {
            background: var(--primary-color) !important;
            color: white !important;
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

    @include('shared.appbar', ['backRoute' => 'drawingTransactionView', 'title' => 'Detail Drawing Transaction', 'marginButtom' => '!mb-3'])

    <div class="ui menu tabular flex justify-center !mt-0">
        <div class="item !px-0 !m-0" data-tab="detail">
            <button class="ui button !text-lg !font-bold !rounded-l-md !rounded-r-none">
                Detail
            </button>
        </div>

        <div class="item !px-0 !m-0" data-tab="approval">
            <button class="ui button !text-lg !font-bold !rounded-none">
                Approval
            </button>
        </div>

        <div class="item !px-0 !m-0" data-tab="steps">
            <button class="ui button !text-lg !font-bold !rounded-r-md !rounded-l-none">
                History
            </button>
        </div>
    </div>

    <div class="ui divider"></div>

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
        document.addEventListener("DOMContentLoaded", function () {

            $('.menu .item').on('click', function (e) {
                e.preventDefault(); // prevent anchor jump behavior if button is inside <a>

                const tab = $(this).data('tab');
                
                // Update URL hash
                window.location.hash = tab;

                // Tell Semantic UI to activate the tab
                $(this).tab('change tab', tab);
            });

            // 1️⃣ First priority: restore tab from validation error
            let restored = @json(old('active_tab'));

            // 2️⃣ Second priority: use URL hash if no validation error
            let hash = location.hash.replace('#', '');

            // 3️⃣ Final fallback: default tab
            let activeTab = restored || hash || "detail";

            // Activate tab
            $('.menu .item').removeClass('active');
            $('.tab.segment').removeClass('active');

            $(`.menu .item[data-tab="${activeTab}"]`).addClass('active');
            $(`.tab.segment[data-tab="${activeTab}"]`).addClass('active');

            // Update all forms' hidden fields
            updateActiveTabInputs(activeTab);

            // Load steps tab if needed
            if (activeTab === 'steps') {
                loadStepsTab();
            }
        });

        // Updates all hidden "active_tab" inputs inside all forms
        function updateActiveTabInputs(tabName) {
            document.querySelectorAll('input[name="active_tab"]').forEach(el => {
                el.value = tabName;
            });
        }

        // Semantic UI tab behavior
        $('.menu .item').tab({
            onVisible: function (tabName) {
                // Keep hidden input updated
                updateActiveTabInputs(tabName);

                if (tabName === 'steps') {
                    loadStepsTab();
                }
            }
        });

        let stepsTabLoaded = false;

        function loadStepsTab() {
            if (stepsTabLoaded) return;
            $("#stepsLoader").addClass("active");

            $.get("{{ route('drawingTransactionSteps', $data->id) }}", function (html) {
                $("#stepsTab").html(html);
                stepsTabLoaded = true;
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
