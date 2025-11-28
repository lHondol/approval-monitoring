@extends('master.layout')

@section('content')
    @include('shared.back-button', ['backRoute' => 'drawingTransactionView'])

    <style>
        /* Remove tab borders & background */
        .ui.tabular.menu .item {
            border: none !important;
            background: transparent !important;
            border-bottom: 1px solid black !important;
        }

        /* Remove active item underline and background */
        .ui.tabular.menu .item.active {
            font-weight: 600;
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

    <div class="ui top attached tabular menu">
        <a class="!text-base item active" data-tab="detail">Detail</a>
        <a class="!text-base item" data-tab="steps">Steps</a>
    </div>

    {{-- DETAIL TAB --}}
    <div class="ui bottom attached tab segment active" data-tab="detail">
        @include('drawing-transaction.tabs.detail-tab', ['data' => $data])
    </div>

    {{-- STEPS TAB (AJAX) --}}
    <div class="ui bottom attached tab segment" data-tab="steps" id="stepsTab">
        <div class="ui active inverted dimmer" id="stepsLoader">
            <div class="ui loader"></div>
        </div>
    </div>

    <script>
    $('.menu .item').tab({
        onVisible: function (tabName) {
            if (tabName === 'steps') loadStepsTab();
        }
    });

    function loadStepsTab() {
        $("#stepsLoader").addClass("active");


    }
    </script>
@endsection
