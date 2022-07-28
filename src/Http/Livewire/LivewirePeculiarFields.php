<?php

namespace Batiscaff\FieldsKit\Http\Livewire;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Class LivewirePeculiarFields.
 * @package Batiscaff\FieldsKit\Http\Livewire
 */
class LivewirePeculiarFields extends Component
{
    public $model;

    public ?int $pfIdForDelete = null;
    public bool $pfIsDeleteConfirmModalOpen = false;
    public $fieldsList;

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'itemAdded' => 'reRenderFieldData',
        'pfItemsIsSorted',
    ];

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->fieldsList = $this->model->peculiarFields;
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::peculiar-fields-section');
    }

    /**
     * @return void
     */
    public function reRenderFieldData(): void
    {
        $this->mount();
        $this->render();
    }

    /**
     * @param int $fieldId
     * @return void
     */
    public function pfAskToDelete(int $fieldId): void
    {
        if (Auth::user()->can(config('fields-kit.permission.peculiar-field.delete'))) {
            $this->pfIdForDelete              = $fieldId;
            $this->pfIsDeleteConfirmModalOpen = true;
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
    public function pfDelete(PeculiarField $peculiarFieldClass): void
    {
        if (!empty($this->pfIdForDelete) && Auth::user()->can(config('fields-kit.permission.peculiar-field.delete'))) {
            $peculiarFieldClass::destroy($this->pfIdForDelete);
            $this->pfIdForDelete = null;
        }

        $this->pfIsDeleteConfirmModalOpen = false;
        $this->emitSelf('refreshComponent');

        $this->emit(config('fields-kit.flash_key'), [
            'message' => __('fields-kit::messages.field-deleted'),
            'type' => 'success',
        ]);
    }

    /**
     * @param int $fieldId
     * @param PeculiarField $peculiarFieldClass
     * @return void
     */
    public function pfCopy(int $fieldId, PeculiarField $peculiarFieldClass): void
    {
        if (Auth::user()->can(config('fields-kit.permission.peculiar-field.copy'))) {
            $field = $peculiarFieldClass::find($fieldId);
            if ($field) {
                $field->createCopy();

                $this->emit(config('fields-kit.flash_key'), [
                    'message' => __('fields-kit::messages.field-copy-created'),
                    'type' => 'success',
                ]);
                $this->emitSelf('itemAdded');
            }

        } else {
            $this->emit(config('fields-kit.flash_key'), [
                'message' => __('fields-kit::messages.copy-item-denied'),
                'type' => 'error',
            ]);
        }
    }

    /**
     * @param array $keys
     * @return void
     */
    public function pfItemsIsSorted(array $keys): void
    {
        $keys = array_flip($keys);

        foreach ($this->fieldsList as $i => $item) {
            $item->sort = $keys[$i];
            $item->save();
        }

        $this->fieldsList = $this->fieldsList->sortBy('sort')->values();

        $this->emit(config('fields-kit.flash_key'), [
            'message' => __('fields-kit::messages.order-changed'),
            'type' => 'success',
        ]);
    }
}
