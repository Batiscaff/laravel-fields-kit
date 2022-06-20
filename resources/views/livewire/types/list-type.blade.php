<div class="card card-info" x-data="listSort('itemsIsSorted')">
    <div class="card-header">
        {{ __('fields-kit::edit.field-content') }}
    </div>
    <div class="card-body">
        @if(!empty($settings['columns']))
        <table class="table json-list-items">
            <thead>
            <tr>
                <th style="width: 50px;"></th>
                @foreach($settings['columns'] as $column)
                    <th class="text-nowrap">{{ $column['label'] }}</th>
                @endforeach
                <th style="width: 50px;"></th>
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
                    @foreach($settings['columns'] as $column)
                        <td>
                            <input type="text" class="form-control"
                                   wire:model.defer="value.{{$i}}.{{ $column['name'] }}">
                        </td>
                    @endforeach
                    <td>
                        <button class="btn btn-info btn-sm"
                                wire:click="removeItem({{ $i }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="{{ count($settings['columns']) + 1 }}">
                        @error('json-list')
                        <span id="json-list-error" class="error invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </td>
                    <td><button class="btn btn-success btn-sm" wire:click="addItem()"><i class="fas fa-plus"></i></button></td>
                </tr>
            </tfoot>
        </table>
        @else
            {{ __("Необходимо настроить поля списка") }}
        @endif
    </div>
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
