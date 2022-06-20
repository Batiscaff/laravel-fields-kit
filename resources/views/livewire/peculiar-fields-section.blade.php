<section id="peculiar-fields-section">
    <div class="card card-secondary">
        @if(empty($model) || !$model->exists)
            <div class="overlay dark">
                <h5 class="text-light">{{ __('fields-kit::section.will-become') }}</h5>
            </div>
        @endif
        <div class="card-header">
            {{ __('fields-kit::section.custom-fields') }}
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
                        <th>{{ __('fields-kit::section.name') }}</th>
                        <th>{{ __('fields-kit::section.title') }}</th>
                        <th>{{ __('fields-kit::section.type') }}</th>
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
                                {{ __('fields-kit::section.edit') }}
                            </a>
                            <button wire:click="pfAskToDelete({{ $field->id }})" class="btn btn-danger btn-sm">
                                <span class="fas fa-trash"></span>
                                {{ __('fields-kit::section.delete') }}
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">
            @livewire('peculiar-field-add-button', ['model' => $this->model])
        </div>
    </div>

    <x-jet-dialog-modal wire:model="pfIsDeleteConfirmModalOpen">
        <x-slot name="title">
            {{ __('fields-kit::section.delete-item') }}
        </x-slot>
        <x-slot name="content">
            <p>{{ __('fields-kit::section.want-to-delete') }}</p>
        </x-slot>
        <x-slot name="footer">
            <button class="btn btn-danger" class="ml-2" wire:click="pfDelete" wire:loading.attr="disabled">
                {{ __('fields-kit::section.delete') }}
            </button>
            <button class="btn btn-secondary" class="ml-2" data-dismiss="modal" aria-label="{{ __('fields-kit::section.cancel') }}">
                {{ __('fields-kit::section.cancel') }}
            </button>
        </x-slot>
    </x-jet-dialog-modal>
</section>
