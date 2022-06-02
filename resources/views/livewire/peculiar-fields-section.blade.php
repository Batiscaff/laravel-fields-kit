<section id="peculiar-fields-section">
    <div class="card card-secondary">
        @if(empty($model) || !$model->exists)
            <div class="overlay dark">
                <h5 class="text-light">{{ __('Custom fields will become available after saving the model.') }}</h5>
            </div>
        @endif
        <div class="card-header">
            {{ __('Custom fields') }}
        </div>
        <div class="card-body p-0">

{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    --}}
{{--                </div>--}}
{{--            </div>--}}

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th style="width: 2%"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($fieldsList as $field)
                    <tr>
                        <td>{{ $field->name }}</td>
                        <td>{{ $field->title }}</td>
                        <td>{{ $field->type }}</td>
                        <td class="text-nowrap">
{{--                            <a class="btn btn-primary btn-sm" href="#">--}}
{{--                                <span class="fas fa-folder"></span>--}}
{{--                                {{ __('View') }}--}}
{{--                            </a>--}}
                            <a class="btn btn-info btn-sm"
                               href="{{ route('fields-kit.peculiar-field-edit', ['currentField' => $field->id], false) }}">
                                <span class="fas fa-pencil-alt"></span>
                                {{ __('Edit') }}
                            </a>
                            <button wire:click="pfAskToDelete({{ $field->id }})" class="btn btn-danger btn-sm">
                                <span class="fas fa-trash"></span>
                                {{ __('Delete') }}
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">
            <button type="button" class="btn btn-success ml-3 mt-2" wire:click="pfAddFieldModal">
                <span class="fas fa-plus"></span>
                {{ __('Add') }}
            </button>
        </div>
    </div>

    <x-jet-dialog-modal wire:model="pfIsAddModalOpen">
        <x-slot name="title">
            {{ __('Add field') }}
        </x-slot>
        <x-slot name="content">
            <div class="field-group">
                <label for="new-field-type-input">{{ __('Select field type') }}</label>
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
            <div class="field-group">
                <label for="new-field-name-input">{{ __('Name') }}</label>
                <input type="text"
                       class="form-control @error('newFieldName') is-invalid @enderror"
                       wire:model="newFieldName">
                @error('newFieldName')
                <span id="new-name-input-error" class="error invalid-feedback block">{{ $message }}</span>
                @enderror
            </div>
            <div class="field-group">
                <label for="new-field-title-input">{{ __('Title') }}</label>
                <input type="text"
                       class="form-control @error('newFieldTitle') is-invalid @enderror"
                       wire:model="newFieldTitle">
                @error('newFieldTitle')
                <span id="new-title-input-error" class="error invalid-feedback block">{{ $message }}</span>
                @enderror
            </div>
        </x-slot>
        <x-slot name="footer">
            <button class="btn btn-success" class="ml-2" wire:click="pfAddField" wire:loading.attr="disabled">
                {{ __('Add') }}
            </button>
            <button class="btn btn-secondary" class="ml-2" data-dismiss="modal" aria-label="{{ __('Cancel') }}">
                {{ __('Cancel') }}
            </button>
        </x-slot>
    </x-jet-dialog-modal>

    <x-jet-dialog-modal wire:model="pfIsDeleteConfirmModalOpen">
        <x-slot name="title">
            {{ __('Delete item') }}
        </x-slot>
        <x-slot name="content">
            <p>{{ __('Are you sure you want to delete this item?') }}</p>
        </x-slot>
        <x-slot name="footer">
            <button class="btn btn-danger" class="ml-2" wire:click="pfDelete" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </button>
            <button class="btn btn-secondary" class="ml-2" data-dismiss="modal" aria-label="{{ __('Cancel') }}">
                {{ __('Cancel') }}
            </button>
        </x-slot>
    </x-jet-dialog-modal>
</section>
