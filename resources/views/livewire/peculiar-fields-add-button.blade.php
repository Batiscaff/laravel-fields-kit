<div>
    @can(config('fields-kit.permission.peculiar-field.add'))
        <button type="button" class="btn btn-success ml-3 mt-2" wire:click="addFieldModal">
            <span class="fas fa-plus"></span>
            {{ __('fields-kit::section.add') }}
        </button>

        <x-jet-dialog-modal wire:model="isAddModalOpen">
            <x-slot name="title">
                {{ __('fields-kit::section.add-field') }}
            </x-slot>
            <x-slot name="content">
                <div class="field-group text-left">
                    <label for="new-field-type-input">{{ __('fields-kit::section.select-field-type') }}</label>
                    <select class="form-control select2  @error('newFieldType') is-invalid @enderror"
                            id="new-type-input"
                            wire:model.defer="newFieldType">
                        <option>--</option>
                        @foreach($typesList as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('newFieldType')
                    <span id="new-type-input-error" class="error invalid-feedback block">{{ $message }}</span>
                    @enderror
                </div>
            @if(!$isFlatGroup)
                <div class="field-group text-left">
                    <label for="new-field-name-input">{{ __('fields-kit::section.name') }}</label>
                    <input type="text"
                           class="form-control @error('newFieldName') is-invalid @enderror"
                           wire:model="newFieldName">
                    @error('newFieldName')
                    <span id="new-name-input-error" class="error invalid-feedback block">{{ $message }}</span>
                    @enderror
                </div>
            @endif
                <div class="field-group text-left">
                    <label for="new-field-title-input">{{ __('fields-kit::section.title') }}</label>
                    <input type="text"
                           class="form-control @error('newFieldTitle') is-invalid @enderror"
                           wire:model="newFieldTitle">
                    @error('newFieldTitle')
                    <span id="new-title-input-error" class="error invalid-feedback block">{{ $message }}</span>
                    @enderror
                </div>
            </x-slot>
            <x-slot name="footer">
                <button class="btn btn-success" class="ml-2" wire:click="addField" wire:loading.attr="disabled">
                    {{ __('fields-kit::section.add') }}
                </button>
                <button class="btn btn-secondary" class="ml-2" data-dismiss="modal" aria-label="{{ __('fields-kit::section.cancel') }}">
                    {{ __('fields-kit::section.cancel') }}
                </button>
            </x-slot>
        </x-jet-dialog-modal>
    @endcan
</div>
