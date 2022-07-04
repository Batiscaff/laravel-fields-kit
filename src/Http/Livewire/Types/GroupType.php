<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Class GroupType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class GroupType extends Component
{
    public PeculiarField $currentField;

    public Collection $value;
    public Collection $settings;

    public ?int $idForDelete = null;
    public bool $isDeleteConfirmModalOpen = false;

    protected $listeners = [
        'save', 'reRenderFieldData', 'itemsIsSorted',
        'itemAdded' => 'reRenderFieldData',
    ];

    /**
     * @param PeculiarField $currentField
     * @return void
     */
    public function mount(PeculiarField $currentField): void
    {
        $this->currentField = $currentField;
        $this->value        = $currentField->getValue();
        $this->settings     = $currentField->settings;

        if (!$currentField->getSettings('group-type')) {
            $this->settings['group-type'] = \Batiscaff\FieldsKit\Types\GroupType::GROUP_TYPE_COMMON;
        }
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::types.group-type');
    }

    /**
     * @return void
     */
    public function save(): void
    {
        $this->currentField->setValue($this->value);
    }

    /**
     * @return void
     */
    public function reRenderFieldData(): void
    {
        $this->mount($this->currentField);
        $this->render();
    }

    /**
     * @param int $itemId
     * @return void
     */
    public function askToDelete(int $itemId): void
    {
        if (Auth::user()->can(config('fields-kit.permission.peculiar-field.delete'))) {
            $this->idForDelete              = $itemId;
            $this->isDeleteConfirmModalOpen = true;
        } else {
            $this->emit(config('fields-kit.flash_key'), [
                'message' => __('fields-kit::messages.delete-item-denied'),
                'type' => 'error',
            ]);
        }
    }

    /**
     * @param PeculiarField $peculiarFieldClass
     * @return void
     */
    public function deleteItem(PeculiarField $peculiarFieldClass): void
    {
        if (!empty($this->idForDelete)) {
            $peculiarFieldClass::destroy($this->idForDelete);
            $this->idForDelete = null;
        }

        $this->isDeleteConfirmModalOpen = false;
        $this->emitSelf('reRenderFieldData');

        $this->emit(config('fields-kit.flash_key'), [
            'message' => __('fields-kit::messages.field-deleted'),
            'type' => 'success',
        ]);
    }

    /**
     * @param array $keys
     * @return void
     */
    public function itemsIsSorted(array $keys): void
    {
        $keys = array_flip($keys);

        foreach ($this->value as $i => $item) {
            $item->sort = $keys[$i];
            $item->save();
        }

        $this->value = $this->value->sortBy('sort')->values();

        $this->emit(config('fields-kit.flash_key'), [
            'message' => __('fields-kit::messages.order-changed'),
            'type' => 'success',
        ]);
    }
}
