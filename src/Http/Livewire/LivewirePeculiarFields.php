<?php

namespace Batiscaff\FieldsKit\Http\Livewire;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;
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

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'itemAdded' => '$refresh',
    ];

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::peculiar-fields-section', [
            'fieldsList' => $this->model->peculiarFields,
        ]);
    }

    /**
     * @param int $fieldId
     * @return void
     */
    public function pfAskToDelete(int $fieldId): void
    {
        $this->pfIdForDelete              = $fieldId;
        $this->pfIsDeleteConfirmModalOpen = true;
    }

    /**
     * @param PeculiarField $peculiarFieldClass
     * @return void
     */
    public function pfDelete(PeculiarField $peculiarFieldClass): void
    {
        if (!empty($this->pfIdForDelete)) {
            $peculiarFieldClass::destroy($this->pfIdForDelete);
            $this->pfIdForDelete = null;
        }

        $this->pfIsDeleteConfirmModalOpen = false;
        $this->emit('refreshComponent');
    }
}
