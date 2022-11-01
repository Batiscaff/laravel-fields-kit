<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;

/**
 * Class ListType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class ListType extends AbstractType
{
    protected $listeners = ['save', 'reRenderFieldData', 'itemsIsSorted', 'setCurrentLanguage'];

    /**
     * @param PeculiarField $currentField
     * @return void
     */
    public function mount(PeculiarField $currentField): void
    {
        parent::mount($currentField);

        if (!$currentField->getSettings('list-type')) {
            $this->settings['list-type'] = \Batiscaff\FieldsKit\Types\ListType::LIST_TYPE_COMMON;
        }
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::types.list-type');
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
        if (empty($this->settings['max_count']) || $this->settings['max_count'] > count($this->value)) {
            $this->value[] = [];
        } else {
            $this->addError('json-list', __('fields-kit::messages.json-list-max-count'));
        }
    }

    /**
     * @param int $key
     * @return void
     */
    public function removeItem(int $key): void
    {
        if (count($this->value) > 1) {
            $this->value = collect($this->value)->forget($key)->values();
        } else {
            $this->value = collect($this->value)->map(function ($item) {
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

        $this->value = collect($this->value)->sortBy(function ($val, $key) use ($keys) {
            return $keys[$key];
        })->values();
    }
}
