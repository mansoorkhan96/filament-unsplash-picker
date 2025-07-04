<div
    x-data="{
        form: null,
        search: @js($search),
        isProcessing: false,
        searching: $wire.entangle('searching').live,
        isMultiple: $wire.entangle('isMultiple'),
        numberOfSelectableImages: $wire.entangle('numberOfSelectableImages'),
        selectedImages: [],
        isSelected: function(id) {
            return this.selectedImages.find(item => item.id == id) != undefined
        },
        get canNotSelectImages() {
            if (! this.isMultiple) return false

            return ! (this.selectedImages.length < this.numberOfSelectableImages)
        },
        toggleImageSelect: function(image) {
            if (this.isSelected(image.id)) {
                this.selectedImages = this.selectedImages.filter(item => item.id != image.id)
            } else {
                if (this.isMultiple) {
                    if (this.canNotSelectImages) return

                    this.selectedImages.push(image)
                } else {
                    this.selectedImages = [image]
                }
            }
        }
    }"
    x-init="
        form = $el.closest('form')

        form?.addEventListener('submit', (event) => {
            isProcessing = true
        })

        $watch('search', value => searching = value != '')

        $watch('selectedImages', value => $wire.dispatch('unsplash-selected-images-updated', value))
    "
    :class="{ 'pointer-events-none': isProcessing }"
    @click.prevent="if (isProcessing) return"
>
    <div
        x-show="isProcessing"
        class="absolute inset-0 z-10 flex items-center justify-center bg-gray-100 bg-opacity-75"
    >
        <x-filament::loading-indicator class="h-12 w-12 text-primary-500" />
    </div>

    <div :class="{ 'opacity-50': isProcessing }">
        <div class="mb-4">
            {{ $this->form }}
        </div>

        <div>
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
                            'pointer-disabled pointer-events-none': canNotSelectImages && ! isSelected(@js($item['id'])),
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
                            @click.stop="toggleImageSelect(@js($item))"
                            type="button"
                            class="absolute inset-0 w-full flex-col justify-between flex"
                        >
                            <div class="px-3 py-2 flex">
                                <x-filament::input.checkbox
                                    class="invisible checked:visible group-hover:visible group-hover:ring-2 group-hover:ring-primary-500 cursor-pointer  group-hover:dark:ring-primary-500"
                                    x-bind:checked="isSelected('{{ $item['id'] }}')"
                                />
                            </div>

                            <div class="group-hover:flex hidden w-full justify-center bg-primary-500 opacity-90">
                                <a
                                    href="{{ $item['user']['links']['html'] }}"
                                    target="_blank"
                                    class="text-sm text-white font-medium underline"
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
                    <p>
                        {!! __('unsplash-picker::unsplash-picker-action.no_search_results') !!}
                    </p>
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
</div>
