<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;

/**
 * Class BoolType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class BoolType extends AbstractType
{
    public mixed $value = '0';

    protected $listeners = ['save', 'refreshComponent' => '$refresh', 'setCurrentLanguage'];

    /**
     * @param PeculiarField $currentField
     * @return void
     */
    public function mount(PeculiarField $currentField): void
    {
        parent::mount($currentField);
        $this->value = $this->value ? '1' : '0';
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
