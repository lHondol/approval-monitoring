<div class="flex flex-col">
    @foreach ($data as $dt)
        <div class="ui !my-3 segment flex justify-between gap-3 p-4 rounded shadow-sm border border-gray-200">
            <div class="flex flex-col flex-wrap justify-center items-start gap-4">
                <p class="m-0"><strong>Done By:</strong> {{ $dt->user->name }}</p>
                <p class="m-0"><strong>Done At:</strong> {{ \Carbon\Carbon::parse($dt->done_at)->format('d M Y H:i:s') }}</p>
                <p class="m-0"><strong>Action Done:</strong> {{ $dt->action_done }}</p>
                @if ($dt->reason)
                    <p class="m-0"><strong>Reason:</strong> {{ $dt->reason }}</p>
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

