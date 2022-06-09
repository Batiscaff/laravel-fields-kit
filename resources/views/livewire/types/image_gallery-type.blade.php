<div class="card card-info" x-data="listSort"
     x-on:livewire-upload-start="isUploading = true"
     x-on:livewire-upload-finish="isUploading = false"
     x-on:livewire-upload-error="isUploading = false"
     x-on:livewire-upload-progress="progress = $event.detail.progress"
>
    <div class="card-header">
        {{ __('fields-kit::edit.field-content') }}
    </div>
    <div class="card-body p-2 position-relative">
        <div class="image-gallery-container row">
            @foreach($value as $i => $image)
                @if(!empty($image))
                    <div class="col-2 d-flex align-items-stretch flex-column sortable"
                         draggable="true"
                         :class="{'bg-gray-light': isTarget}"
                         x-data="{isTarget:false}"
                         @dragstart.self="dragStart"
                         @dragover.prevent="isTarget=true"
                         @dragleave.prevent="isTarget=false"
                         @drop.prevent="dropEl"
                         @mousedown="this.handle=$event.target"
                         @mouseup="this.handle = null;"
                         data-pos="{{ $i }}"
                    >
                        <div class="card d-flex flex-fill height-control">
                        @if(is_array($image))
                            <div class="card-body p-1 text-center">
                                @if(Storage::disk('public')->exists($image['src']))
                                    <img src="{{ Storage::url($image['src']) }}" class="img-fluid sort-handle mh-100"
                                         draggable="false">
                                @endif
                            </div>
                            <div class="card-footer">
                                <span class="mailbox-attachment-size clearfix mt-1 text-gray">
                                    @if(Storage::disk('public')->exists($image['src']))
                                        <span>{{ formatBytes(Storage::disk('public')->size($image['src'])) }}</span>
                                    @endif
                                    <button href="#"class="btn btn-default btn-sm float-right"
                                            wire:click="removeItem({{ $i }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </span>
                            </div>
                        @elseif ($image instanceof Livewire\TemporaryUploadedFile)
                            <div class="card-body p-1 text-center">
                                <img src="{{ $image->temporaryUrl() }}" class="img-fluid sort-handle mh-100"
                                     draggable="false">
                            </div>
                            <div class="card-footer">
                                <span class="mailbox-attachment-size clearfix mt-1 text-gray">
                                    <span>{{ formatBytes($image->getSize()) }}</span>
                                    <button href="#"class="btn btn-default btn-sm float-right"
                                            wire:click="removeImageGalleryItem({{ $i }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </span>
                            </div>
                        @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div>
            <div class="input-group">
                <div class="custom-file">
                    <input id="field-value"
                           type="file"
                           class="custom-file-input"
                           multiple="true"
                           data-preview-file-type="text"
                           wire:loading.attr="disabled"
                           wire:model="value">
                    <label class="custom-file-label" for="field-value">{{ __('Выберите файл(ы)') }}</label>
                </div>
            </div>
            <div x-show="isUploading">
                <progress max="100" x-bind:value="progress"></progress>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', function () {
            Alpine.data('listSort', function() {
                return {
                    isUploading: false,
                    progress: 0,

                    handle: null,
                    method: null,
                    draggable: null,
                    index: function (el) {
                        return Array.prototype.indexOf.call(el.parentNode.children, el);
                    },
                    dragStart: function (e) {
                        if (handle && !handle.classList.contains('sort-handle')) {
                            e.preventDefault();
                            return false;
                        }

                        this.startContainer = e.target.parentNode;
                        this.startIndex = this.index(e.target);
                        this.draggable = e.target;
                    },
                    dragEnd: function (e) {
                        if (e.target.id === 'drag-handle') {
                            e.target.id = '';
                        }
                    },
                    dropEl: function (e) {
                        const target = e.target.closest('.sortable');
                        const el = this.draggable;

                        if (this.index(target) < this.index(el)) {
                            el.parentNode.insertBefore(el, target);
                        } else {
                            el.parentNode.insertBefore(el, target.nextElementSibling);
                        }

                        this.isTarget = false
                        this.draggable = null;

                        const rows = document.getElementsByClassName('sortable');
                        const indexes = Array.prototype.map.call(rows, function(el) {
                            return el.getAttribute('data-pos');
                        })

                        Livewire.emit('itemsIsSorted', indexes);
                    }
                };
            });
        });
    </script>
@endpush
