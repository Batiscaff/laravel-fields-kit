@php
if ($settings['list-type'] === \Batiscaff\FieldsKit\Types\ListType::LIST_TYPE_FLAT) {
    $columnsCount = 1;
} else {
    $columnsCount = count($settings['columns']);
}
@endphp
<div class="card card-info" x-data="listSort('itemsIsSorted')">
    <div class="card-header">
        {{ __('fields-kit::edit.field-content') }}
    </div>
    <div class="card-body">
        @if(!empty($settings['columns']) || $settings['list-type'] === \Batiscaff\FieldsKit\Types\ListType::LIST_TYPE_FLAT)
        <table class="table json-list-items">
            <thead>
            <tr>
                @can(config('fields-kit.permission.peculiar-field.update-value'))
                    <th style="width: 50px;"></th>
                @endcan
                @if($settings['list-type'] === \Batiscaff\FieldsKit\Types\ListType::LIST_TYPE_FLAT)
                    <th class="text-nowrap">{{ __('fields-kit::edit.list-value') }}</th>
                @else
                    @foreach($settings['columns'] as $column)
                        <th class="text-nowrap">{{ $column['label'] }}</th>
                    @endforeach
                @endif
                @can(config('fields-kit.permission.peculiar-field.update-value'))
                    <th style="width: 50px;"></th>
                @endcan
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
                    @can(config('fields-kit.permission.peculiar-field.update-value'))
                        <td><i class="fas fa-bars sort-handle" style="cursor: move"></i></td>
                    @endcan
                    @if($settings['list-type'] === \Batiscaff\FieldsKit\Types\ListType::LIST_TYPE_FLAT)
                        <td>
                            @can(config('fields-kit.permission.peculiar-field.update-value'))
                                <input type="text" class="form-control"
                                       wire:model.defer="value.{{$i}}.value">
                            @else
                                <input type="text" class="form-control"
                                       value="{{ $value[$i]['value'] }}"
                                       readonly>
                            @endcan
                        </td>
                    @else
                        @foreach($settings['columns'] as $column)
                            <td>
                                @can(config('fields-kit.permission.peculiar-field.update-value'))
                                    <input type="text" class="form-control"
                                           wire:model.defer="value.{{$i}}.{{ $column['name'] }}">
                                @else
                                    <input type="text" class="form-control"
                                           value="{{ $value[$i][$column['name']] }}"
                                           readonly>
                                @endcan
                            </td>
                        @endforeach
                    @endif
                    @can(config('fields-kit.permission.peculiar-field.update-value'))
                        <td>
                            <button class="btn btn-info btn-sm"
                                    wire:click="removeItem({{ $i }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    @endcan
                </tr>
            @endforeach
            </tbody>
            @can(config('fields-kit.permission.peculiar-field.update-value'))
                <tfoot>
                    <tr>
                        <td colspan="{{ $columnsCount + 1 }}">
                            @error('json-list')
                            <span id="json-list-error" class="error invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </td>
                        <td><button class="btn btn-success btn-sm" wire:click="addItem()"><i class="fas fa-plus"></i></button></td>
                    </tr>
                </tfoot>
            @endcan
        </table>
        @else
            {{ __('fields-kit::edit.list-needs-configure') }}
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
