<div>
    <div
        class="mb-4"
        x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('unsplash-picker', 'mansoor/unsplash-picker'))]"
    >
        {{ $this->form }}
    </div>

    <div>
        @if (blank($search))
            <p class="text-center">{{ __('unsplash-picker::unsplash-picker.empty_search') }}</p>
        @else
            <div @class([
                'grid grid-cols-3 lg:grid-cols-4 gap-4' =>
                    filled($this->getImages) && $this->shouldUseSquareDisplay,
                'columns-3 space-y-4 lg:columns-4' =>
                    filled($this->getImages) && !$this->shouldUseSquareDisplay,
            ])>
                @forelse ($this->getImages as $item)
                    <div
                        x-data="{ picked: false }"
                        class="group relative w-full cursor-pointer overflow-hidden rounded-lg ring-gray-950/10 hover:ring-2 hover:ring-primary-600 hover:ring-offset-2 dark:ring-white/20 dark:hover:ring-primary-500 dark:hover:ring-offset-black"
                    >
                        <span @click="picked = true; $wire.$parent.callMountedFormComponentAction(@js($item))">
                            <img
                                @class([
                                    'aspect-square' => $this->shouldUseSquareDisplay,
                                    'pointer-events-none  w-full object-cover',
                                ])
                                src="{{ $item['urls']['thumb'] }}"
                            >
                        </span>

                        <div
                            x-show="picked"
                            x-cloak
                            class="absolute inset-0 flex h-full w-full flex-col items-center justify-center bg-gray-900 bg-opacity-60"
                        >
                            @svg('heroicon-c-arrow-path','h-8 w-8 animate-spin text-white')
                        </div>

                        <div
                            class="absolute bottom-0 hidden w-full items-center justify-center bg-black opacity-80 group-hover:flex">
                            <a
                                href="{{ $item['user']['links']['html'] }}?utm_source=goldencarers&utm_medium=referral&utm_campaign=api-credit"
                                target="_blank"
                                class="text-xs text-white underline"
                            >
                                {{ str($item['user']['name'])->limit(20) }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center">
                        <p>{{ __('unsplash-picker::unsplash-picker.no_results') }}</p>
                        <p>{{ __('unsplash-picker::unsplash-picker.try_again') }}</p>
                    </div>
                @endforelse
            </div>
        @endif

        @if (filled($search) && count($this->getImages) > 0)
            <div class="mt-6 flex justify-between">
                {{ $this->previousPageAction }}
                {{ $this->nextPageAction }}
            </div>
        @endif
    </div>
</div>
