<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Illuminate\Contracts\View\View;

/**
 * Class HtmlType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class HtmlType extends AbstractType
{
    public mixed $value = '';

    protected $listeners = ['save', 'refreshComponent' => '$refresh', 'setCurrentLanguage'];

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::types.html-type');
    }
}
