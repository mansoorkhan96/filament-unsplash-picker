<div
    x-data="{ state: $wire.$entangle('{{ $statePath = $getStatePath() }}') }"
    x-on:unsplash-selected-images-updated.window="state = $event.detail"
>
</div>
