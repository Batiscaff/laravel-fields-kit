<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Class HtmlType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class HtmlType extends Component
{
    public PeculiarField $currentField;

    public string $value = '';

    protected $listeners = ['save', 'refreshComponent' => '$refresh'];

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
        return view('fields-kit::types.html-type');
    }

    /**
     * @return void
     */
    public function save(): void
    {
        $this->currentField->setValue($this->value);
    }
}
