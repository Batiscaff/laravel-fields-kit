<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Class StringType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class StringType extends Component
{
    public PeculiarField $currentField;

    public string $value = '';

    protected $listeners = ['refreshComponent' => '$refresh'];

    /**
     * @param PeculiarField $currentField
     * @return void
     */
    public function mount(PeculiarField $currentField): void
    {
        $this->currentField = $currentField;
        $this->value        = $currentField->getValue();
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::types.string-type');
    }

    /**
     * @param bool|null $isBackward
     * @return void
     */
    public function save(?bool $isBackward = false): void
    {
        $this->currentField->setValue($this->value);
    }
}
