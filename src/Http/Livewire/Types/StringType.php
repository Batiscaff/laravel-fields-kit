<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Illuminate\Contracts\View\View;

/**
 * Class StringType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class StringType extends AbstractType
{
    public mixed $value = '';

    protected $listeners = ['save', 'refreshComponent' => '$refresh', 'setCurrentLanguage'];

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::types.string-type');
    }
}
