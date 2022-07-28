<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Class BoolType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class BoolType extends Component
{
    public PeculiarField $currentField;

    public string $value = '0';

    protected $listeners = ['save', 'refreshComponent' => '$refresh'];

    /**
     * @param PeculiarField $currentField
     * @return void
     */
    public function mount(PeculiarField $currentField): void
    {
        $this->currentField = $currentField;
        $this->value        = $currentField->getValue() ? '1' : '0';
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::types.bool-type');
    }

    /**
     * @return void
     */
    public function save(): void
    {
        $this->currentField->setValue((bool) $this->value);
    }
}
