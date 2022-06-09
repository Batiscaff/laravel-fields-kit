<div class="card card-info" x-data="listSort()">
    <div class="card-header">
        {{ __('fields-kit::edit.field-content') }}
    </div>
    <div class="card-body">
        <table class="table json-list-items">
            <thead>
            <tr>
                <td style="width: 50px;"></td>
                @foreach($settings['columns'] as $column)
                    <td class="text-nowrap">{{ $column['label'] }}</td>
                @endforeach
                <td style="width: 50px;"></td>
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
                    <td colspan="{{ count($settings['columns']) + 1 }}"></td>
                    <td><button class="btn btn-success btn-sm" wire:click="addItem()"><i class="fas fa-plus"></i></button></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', function () {
            Alpine.data('listSort', function() {
                return {
                    handle: null,
                    method: null,
                    draggable: null,
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

                        const rows = document.getElementsByClassName('sortable');
                        const indexes = Array.prototype.map.call(rows, function(el) {return el.getAttribute('data-pos')})

                        Livewire.emit('itemsIsSorted', indexes);
                    }
                };
            });
        });
    </script>
@endpush
