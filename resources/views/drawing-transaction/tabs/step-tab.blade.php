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

<div class="flex flex-col gap-3">
    @foreach ($data as $dt)
        <div class="ui segment flex justify-between gap-3 p-4 rounded shadow-sm border border-gray-200">
            <div class="flex flex-col flex-wrap justify-center items-start gap-4">
                <p class="m-0"><strong>Done By:</strong> {{ $dt->user->name }}</p>
                <p class="m-0"><strong>Done At:</strong> {{ \Carbon\Carbon::parse($dt->done_at)->format('d M Y H:i:s') }}</p>
                <p class="m-0"><strong>Action Done:</strong> {{ $dt->action_done }}</p>
                @if ($dt->reject_reason)
                    <p class="m-0"><strong>Reject Reason:</strong> {{ $dt->reject_reason }}</p>
                @endif
            </div>
            <div>
                @if ($dt->rejected_file)
                    <div class="flex flex-wrap gap-2" 
                        id="previewContainer_{{ $dt->id }}" 
                        style="min-height: 150px;">
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<script>
    // Prepare data for JS
    const rejectedFilesData = @json($data->map(fn($s) => [
        'id' => $s->id,
        'filepath' => optional($s->rejected_file)->filepath
    ]));

    // Call your function
    initStepPreview(rejectedFilesData);
</script>

