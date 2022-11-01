<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Illuminate\Contracts\View\View;
use Livewire\WithFileUploads;

/**
 * Class ImageType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class ImageType extends AbstractType
{
    use WithFileUploads;

    protected $listeners = ['save', 'reRenderFieldData', 'itemsIsSorted', 'setCurrentLanguage'];

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::types.image-type');
    }

    /**
     * @return void
     */
    public function save(): void
    {
        $this->currentField->setValue($this->value);
        $this->reRenderFieldData();
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
     * @return void
     */
    public function removeImage(): void
    {
        $this->value = null;
    }
}
