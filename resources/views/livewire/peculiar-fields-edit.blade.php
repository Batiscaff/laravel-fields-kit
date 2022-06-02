<x-slot name="header">
    {{ __('fields-kit::edit.title') }}
</x-slot>

<section id="peculiar-field-form">
    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="small-box bg-white">
                <div class="inner">
                    <table>
                        <tr>
                            <th>{{ __('fields-kit::edit.field-title') }}:</th>
                            <td>{{ $this->currentField->title }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('fields-kit::edit.field-name') }}:</th>
                            <td>{{ $this->currentField->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('fields-kit::edit.field-type') }}:</th>
                            <td>{{ $this->currentField->type }}</td>
                        </tr>
                    </table>
                </div>
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <a class="small-box-footer"
                   href="#"
                   wire:click.prevent="editField()">
                    {{ __('fields-kit::edit.field-edit') }} <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @livewire($this->livewireComponent, ['currentField' => $this->currentField])
        </div>
    </div>

    <x-jet-dialog-modal wire:model="isEditModalOpen">
        <x-slot name="title">
            {{ __('fields-kit::edit.main-attributes-header') }}
        </x-slot>
        <x-slot name="content">
            <div class="field-group">
                <label for="field-new-name-input">{{ __('fields-kit::edit.field-name') }}</label>
                <input type="text"
                       class="form-control @error('newName') is-invalid @enderror"
                       id="field-new-name-input"
                       wire:model.defer="newName">
                @error('newName')
                <span id="new-name-input-error" class="error invalid-feedback block">{{ $message }}</span>
                @enderror
            </div>
            <div class="field-group">
                <label for="field-new-title-input">{{ __('fields-kit::edit.field-title') }}</label>
                <input type="text"
                       class="form-control @error('newTitle') is-invalid @enderror"
                       id="field-new-title-input"
                       wire:model.defer="newTitle">
                @error('newTitle')
                <span id="new-title-input-error" class="error invalid-feedback block">{{ $message }}</span>
                @enderror
            </div>
            @include($this->settingsView)
        </x-slot>
        <x-slot name="footer">
            <button class="btn btn-success" class="ml-2" wire:click="updateField" wire:loading.attr="disabled">
                {{ __('fields-kit::edit.save') }}
            </button>
            <button class="btn btn-secondary" class="ml-2" data-dismiss="modal" aria-label="{{ __('fields-kit::edit.cancel') }}">
                {{ __('fields-kit::edit.cancel') }}
            </button>
        </x-slot>
    </x-jet-dialog-modal>
</section>
