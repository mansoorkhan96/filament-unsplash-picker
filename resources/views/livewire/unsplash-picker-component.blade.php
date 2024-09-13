<div
    x-data="{
        search: '',
        searching: $wire.entangle('searching').live,
        isMultiple: $wire.entangle('isMultiple'),
        selectedImages: [],
        isSelected: function(id) {
            return this.selectedImages.find(item => item.id == id) != undefined
        },
        toggle: function(image) {
            if (this.isSelected(image.id)) {
                this.selectedImages = this.selectedImages.filter(item => item.id != image.id)
            } else {
                if (this.isMultiple) {
                    this.selectedImages.push(image)
                } else {
                    this.selectedImages = [image]
                }
            }
        }
    }"
    x-init="function() {
        $watch('search', value => {
            console.log(searching)
            searching = value != ''
            console.log(searching)
        })

        $watch('selectedImages', value => $wire.dispatch('unsplash-selected-images-updated', value))

        {{-- TODO: remove --}}
        {{-- $watch(
            'selectedImages',
            function(value) {
                console.log(value)
                $wire.dispatch('unsplash-selected-images-updated', value)
            }
        ) --}}
    }"
>
    <div class="mb-4">
        {{ $this->form }}
    </div>

    <div>
        <p x-show="search == ''" class="text-center">
            {{ __('unsplash-picker::unsplash-picker.empty_search') }}
        </p>

        <div x-show="searching" class="flex justify-center py-4">
            <x-filament::loading-indicator class="h-8 w-8 text-primary-500" />
        </div>

        <div
            x-show="! searching && search != ''"
            @class([
                'grid grid-cols-3 lg:grid-cols-4 gap-4' => $this->shouldUseSquareDisplay,
                'columns-3 space-y-4 lg:columns-4' => !$this->shouldUseSquareDisplay,
            ])>
            @foreach ($this->getImages as $index => $item)
                <div
                    :class="{
                        'border-primary-500': isSelected(@js($item['id'])),
                        'border-transparent': ! isSelected(@js($item['id'])),
                        'group relative w-full cursor-pointer overflow-hidden rounded-xl border-[2.5px] hover:border-primary-500': true
                    }">
                    <img
                        @class([
                            'aspect-square' => $this->shouldUseSquareDisplay,
                            'pointer-events-none w-full object-cover',
                        ])
                        src="{{ $item['urls']['thumb'] }}"
                    >

                    <button
                        @click="toggle(@js($item))"
                        type="button"
                        class="absolute inset-0 w-full flex-col justify-between flex"
                    >
                        <div class="px-3 py-2">
                            <x-filament::input.checkbox class="invisible checked:visible group-hover:visible group-hover:ring-2 group-hover:ring-primary-500 group-hover:dark:ring-primary-500" x-bind:checked="isSelected('{{ $item['id'] }}')" />
                        </div>

                        <div class="group-hover:flex hidden w-full justify-center bg-black opacity-90">
                            <a
                                href="{{ $item['user']['links']['html'] }}?utm_source=goldencarers&utm_medium=referral&utm_campaign=api-credit"
                                target="_blank"
                                class="text-xs text-white underline"
                            >
                                {{ str($item['user']['name'])->limit(20) }}
                            </a>
                        </div>
                    </button>
                </div>
            @endforeach
        </div>

        @if (blank($this->getImages))
            <div x-show="! searching && search != ''" class="text-center" >
                <p>{{ __('unsplash-picker::unsplash-picker.no_results') }}</p>
                <p>{{ __('unsplash-picker::unsplash-picker.try_again') }}</p>
            </div>
        @endif

        @if (count($this->getImages) > 0)
            <div x-show="! searching && search != ''" class="mt-6 flex justify-between">
                {{ $this->previousPageAction }}
                {{ $this->nextPageAction }}
            </div>
        @endif
    </div>
</div>
