<?php

namespace Batiscaff\FieldsKit\Http\Livewire;

use Batiscaff\FieldsKit\Traits\Multilingual;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Class LivewirePeculiarFieldLanguageFlag.
 * @package Batiscaff\FieldsKit\Http\Livewire
 */
class LivewirePeculiarFieldLanguageFlag extends Component
{
    use Multilingual;

    protected $listeners = ['reRenderFieldData', 'setCurrentLanguage'];

    /**
     * @return void
     */
    public function mount(): void
    {
        if (empty($this->currentLanguage)) {
            $this->currentLanguage = config('fields-kit.multilingual.default_language', 'ru');
        }
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::peculiar-fields-language-flag');
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
     * @return string
     */
    public function getLanguageFlag(): string
    {
        $langSettings = config('fields-kit.multilingual.languages')[$this->currentLanguage] ?? [];
        return $langSettings['flag'] ?? '';
    }
}
