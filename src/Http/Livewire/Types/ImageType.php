<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Class ImageType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class ImageType extends Component
{
    use WithFileUploads;

    public PeculiarField $currentField;

    public $value;
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
