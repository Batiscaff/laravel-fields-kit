<div>
    <div class="card card-info" x-data="listSort('itemsIsSorted')">
        <div class="card-header">
            {{ __('fields-kit::edit.field-content') }}
        </div>
        <div class="card-body">
            <table class="table json-list-items">
                <thead>
                <tr>
                    <th style="width: 50px;"></th>
                    <th>{{ __('fields-kit::section.name') }}</th>
                    <th>{{ __('fields-kit::section.title') }}</th>
                    <th>{{ __('fields-kit::section.type') }}</th>
                    <th style="width: 2%;"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($value as $i => $item)
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
                        <td><i class="fas fa-bars sort-handle" style="cursor: move"></i></td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->type }}</td>
                        <td class="text-nowrap">
                            <a class="btn btn-info btn-sm"
                               href="{{ route('fields-kit.peculiar-field-edit', ['currentField' => $item->id], false) }}">
                                <span class="fas fa-pencil-alt"></span>
                                {{ __('fields-kit::section.edit') }}
                            </a>
                            <button wire:click="askToDelete({{ $item->id }})" class="btn btn-danger btn-sm">
                                <span class="fas fa-trash"></span>
                                {{ __('fields-kit::section.delete') }}
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">
                            @error('json-list')
                            <span id="json-list-error" class="error invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </td>
                        <td class="text-right">
                            @livewire('peculiar-field-add-button', ['model' => $this->currentField])
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <x-jet-dialog-modal wire:model="isDeleteConfirmModalOpen">
        <x-slot name="title">
            {{ __('fields-kit::section.delete-item') }}
        </x-slot>
        <x-slot name="content">
            <p>{{ __('fields-kit::section.want-to-delete') }}</p>
        </x-slot>
        <x-slot name="footer">
            <button class="btn btn-danger" class="ml-2" wire:click="deleteItem" wire:loading.attr="disabled">
                {{ __('fields-kit::section.delete') }}
            </button>
            <button class="btn btn-secondary" class="ml-2" data-dismiss="modal" aria-label="{{ __('fields-kit::section.cancel') }}">
                {{ __('fields-kit::section.cancel') }}
            </button>
        </x-slot>
    </x-jet-dialog-modal>
</div>

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

                        Livewire.emit('itemsIsSorted', indexes);
                    }
                };
            });
        });
    </script>
@endpush
