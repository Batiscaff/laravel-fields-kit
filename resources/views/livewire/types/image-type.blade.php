<div class="card card-info" x-data="{progress: 0, isUploading: false}"
     x-on:livewire-upload-start="isUploading = true"
     x-on:livewire-upload-finish="isUploading = false"
     x-on:livewire-upload-error="isUploading = false"
     x-on:livewire-upload-progress="progress = $event.detail.progress"
>
    <div class="card-header">
        {{ __('fields-kit::edit.field-content') }}
    </div>
    <div class="card-body p-2 position-relative">
        <div class="image-container row">
            @if(!empty($value))
                <div class="col-2 d-flex align-items-stretch flex-column">
                    <div class="card d-flex flex-fill height-control">
                    @if ($value instanceof Livewire\TemporaryUploadedFile)
                        <div class="card-body p-1 text-center">
                            <img src="{{ $value->temporaryUrl() }}" class="img-fluid sort-handle mh-100"
                                 draggable="false">
                        </div>
                        <div class="card-footer">
                        <span class="mailbox-attachment-size clearfix mt-1 text-gray">
                            <span>{{ formatBytes($value->getSize()) }}</span>
                            @can(config('fields-kit.permission.peculiar-field.update-value'))
                                <button href="#"class="btn btn-default btn-sm float-right"
                                        wire:click="removeImage()">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endcan
                        </span>
                        </div>
                    @else
                        <div class="card-body p-1 text-center">
                            @if(Storage::disk('public')->exists($value['src']))
                                <img src="{{ Storage::url($value['src']) }}" class="img-fluid sort-handle mh-100"
                                     draggable="false">
                            @endif
                        </div>
                        <div class="card-footer">
                            <span class="mailbox-attachment-size clearfix mt-1 text-gray">
                                @if(Storage::disk('public')->exists($value['src']))
                                    <span>{{ formatBytes(Storage::disk('public')->size($value['src'])) }}</span>
                                @endif
                                @can(config('fields-kit.permission.peculiar-field.update-value'))
                                    <button href="#"class="btn btn-default btn-sm float-right"
                                            wire:click="removeImage()">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endcan
                            </span>
                        </div>
                    @endif
                    </div>
                </div>
            @endif
        </div>
        @can(config('fields-kit.permission.peculiar-field.update-value'))
            <div>
                <div class="input-group">
                    <div class="custom-file">
                        <input id="field-value"
                               type="file"
                               class="custom-file-input"
                               data-preview-file-type="text"
                               wire:loading.attr="disabled"
                               wire:model="value">
                        <label class="custom-file-label" for="field-value">{{ __('fields-kit::edit.select-file') }}</label>
                    </div>
                </div>
                <div x-show="isUploading">
                    <progress max="100" x-bind:value="progress"></progress>
                </div>
            </div>
        @endcan
    </div>
</div>
