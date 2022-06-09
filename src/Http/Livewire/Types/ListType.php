<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * Class ListType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class ListType extends Component
{
    public PeculiarField $currentField;

    public Collection $value;
    public string $columns = '[]';
    public Collection $settings;

    protected $listeners = ['save', 'reRenderFieldData', 'itemsIsSorted'];

    /**
     * @param PeculiarField $currentField
     * @return void
     */
    public function mount(PeculiarField $currentField): void
    {
        $this->currentField = $currentField;
        $this->value        = $currentField->getValue();
        $this->settings     = $currentField->settings;

    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::types.list-type');
    }

    /**
     * @return void
     */
    public function save(): void
    {
        $this->currentField->setValue($this->value);
    }

    public function reRenderFieldData()
    {
        $this->mount($this->currentField);
        $this->render();
    }

    /**
     * @return void
     */
    public function addItem(): void
    {
        $this->value[] = [];
    }

    /**
     * @param int $key
     * @return void
     */
    public function removeItem(int $key): void
    {
        if (count($this->value) > 1) {
            $this->value = $this->value->forget($key)->values();
        } else {
            $this->value = $this->value->map(function ($item) {
                data_set($item, '*', '');
                return $item;
            });
        }
    }

    /**
     * @param array $keys
     * @return void
     */
    public function itemsIsSorted(array $keys): void
    {
        $keys = array_flip($keys);

        $this->value = $this->value->sortBy(function ($val, $key) use ($keys) {
            return $keys[$key];
        })->values();
    }
}
