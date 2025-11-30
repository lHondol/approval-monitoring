<div class="mb-10 flex items-center gap-8 pb-8 border-b {{ $marginButtom ?? '!mb-8' }}">
    @include('shared.back-button', ['backRoute' => $backRoute])
    <div class="">
        <span class="text-2xl font-bold uppercase">{{ $title }}</span>
    </div>
</div>