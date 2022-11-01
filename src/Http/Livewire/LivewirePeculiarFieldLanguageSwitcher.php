<?php

namespace Batiscaff\FieldsKit\Http\Livewire;

use Batiscaff\FieldsKit\Traits\Multilingual;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Class LivewirePeculiarFieldLanguageSwitcher.
 * @package Batiscaff\FieldsKit\Http\Livewire
 */
class LivewirePeculiarFieldLanguageSwitcher extends Component
{
    use Multilingual;

    protected $listeners = ['reRenderFieldData'];

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->currentLanguage = config('fields-kit.multilingual.default_language', 'ru');
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::peculiar-fields-language-switcher');
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
     * @param string $languageKey
     * @return void
     */
    public function setCurrentLanguage(string $languageKey): void
    {
        $this->currentLanguage = $languageKey;
        $this->emit('setCurrentLanguage', $languageKey);
    }
}
