<section id="peculiar-fields-section" x-data="listSort('itemsIsSorted')">
    <div class="card card-secondary">
        @if(empty($model) || !$model->exists)
            <div class="overlay dark">
                <h5 class="text-light">{{ __('fields-kit::section.will-become') }}</h5>
            </div>
        @endif
        <div class="card-header">
            {{ __('fields-kit::section.custom-fields') }}
        </div>
        @can(config('fields-kit.permission.peculiar-field.list'))
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            @can(config('fields-kit.permission.peculiar-field.update-value'))
                                <th style="width: 50px;"></th>
                            @endcan
                            <th>{{ __('fields-kit::section.title') }}</th>
                            <th>{{ __('fields-kit::section.name') }}</th>
                            <th>{{ __('fields-kit::section.type') }}</th>
                            <th>{{ __('fields-kit::section.value') }}</th>
                            <th style="width: 2%"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($fieldsList as $i => $field)
                        <tr draggable="true"
                            class="sortable"
                            :class="{'bg-gray-light': isTarget}"
                            x-data="{isTarget:false}"
                            @dragstart.self="dragStart"
                            @dragover.prevent="isTarget=true"
                            @dragleave.prevent="isTarget=false"
                            @drop.prevent="dropEl"
                            @mousedown="this.handle=$event.target"
                            @mouseup="this.handle = null;"
                            data-pos="{{$i}}"
                        >
                            @can(config('fields-kit.permission.peculiar-field.update-value'))
                                <td><i class="fas fa-bars sort-handle" style="cursor: move"></i></td>
                            @endcan
                            <td>{{ $field->title }}</td>
                            <td>{{ $field->name }}</td>
                            <td>{{ $field->typeAsString }}</td>
                            <td>{!! $field->getShortValue() !!}</td>
                            <td class="text-nowrap">
                                @can(config('fields-kit.permission.peculiar-field.view'))
                                    <a class="btn btn-info btn-sm"
                                       href="{{ route('fields-kit.peculiar-field-edit', ['currentField' => $field->id], false) }}">
                                        <span class="fas fa-pencil-alt"></span>
                                        {{ __('fields-kit::section.edit') }}
                                    </a>
                                @endcan
                                @can(config('fields-kit.permission.peculiar-field.delete'))
                                    <button wire:click="pfAskToDelete({{ $field->id }})" class="btn btn-danger btn-sm">
                                        <span class="fas fa-trash"></span>
                                        {{ __('fields-kit::section.delete') }}
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer border-top">
                @livewire('peculiar-field-add-button', ['model' => $this->model])
            </div>
        @else
            <div class="card-body">
                {{ __('fields-kit::section.custom-fields-list-unavailable') }}
            </div>
        @endcan
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

@push('scripts')
    <script>
        document.addEventListener('alpine:init', function () {
            Alpine.data('listSort', function() {
                return {
                    root: null,
                    handle: null,
                    method: null,
                    draggable: null,
                    init: function() {
                        this.root = this.$root;
                    },
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
                        const target = e.target.closest('tr');
                        const el = this.draggable;

                        if (this.index(target) < this.index(el)) {
                            el.parentNode.insertBefore(el, target);
                        } else {
                            el.parentNode.insertBefore(el, target.nextElementSibling);
                        }

                        this.isTarget = false
                        this.draggable = null;

                        const rows = this.root.getElementsByClassName('sortable');
                        const indexes = Array.prototype.map.call(rows, function(el) {return el.getAttribute('data-pos')})

                        Livewire.emit('pfItemsIsSorted', indexes);
                    }
                };
            });
        });
    </script>
@endpush
