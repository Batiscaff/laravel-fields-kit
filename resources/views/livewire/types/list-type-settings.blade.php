<div class="field-group pt-3">
    <label for="field-list-type">{{ __('fields-kit::edit.settings-list-type') }}</label>
    <select class="form-control"
            id="field-list-type"
            wire:model="settings.list-type">
        <option value="{{ \Batiscaff\FieldsKit\Types\ListType::LIST_TYPE_COMMON }}">
            {{ __('fields-kit::edit.list-type-common') }}
        </option>
        <option value="{{ \Batiscaff\FieldsKit\Types\ListType::LIST_TYPE_FLAT }}">
            {{ __('fields-kit::edit.list-type-flat') }}
        </option>
    </select>
</div>
@if(Arr::get($settings, 'list-type') === \Batiscaff\FieldsKit\Types\ListType::LIST_TYPE_COMMON)
    <div class="field-group pt-3" x-data="columnsSort()">
        <label for="field-columns">{{ __('fields-kit::edit.settings-list-columns') }}</label>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px;"></th>
                    <th class="text-nowrap">{{ __('fields-kit::edit.settings-list-columns-name') }}</th>
                    <th class="text-nowrap">{{ __('fields-kit::edit.settings-list-columns-label') }}</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($settings['columns'] ?? [] as $i => $item)
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
                    <td>
                        <input type="text" class="form-control"
                               wire:model.defer="settings.columns.{{$i}}.name">
                    </td>
                    <td>
                        <input type="text" class="form-control"
                               wire:model.defer="settings.columns.{{$i}}.label">
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm"
                                wire:click="removeColumn({{ $i }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3"></td>
                <td><button class="btn btn-success btn-sm" wire:click="addColumn()"><i class="fas fa-plus"></i></button></td>
            </tr>
            </tfoot>
        </table>
    </div>
@endif
<div class="field-group pt-3">
    <label for="field-max-count">{{ __('fields-kit::edit.settings-max-number') }}</label>
    <input type="text"
           class="form-control"
           id="field-max-count"
           wire:model="settings.max_count">
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', function () {
            Alpine.data('columnsSort', function() {
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

                        Livewire.emit('columnsIsSorted', indexes);
                    }
                };
            });
        });
    </script>
@endpush
