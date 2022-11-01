<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Support\Arr;
use Livewire\Component;

/**
 * Class AbstractType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
abstract class AbstractType extends Component
{
    public PeculiarField $currentField;
    public mixed $value = null;
    public mixed $settings;
    public ?string $currentLanguage;
    public array $multilingualCache;

    /**
     * @param PeculiarField $currentField
     * @return void
     */
    public function mount(PeculiarField $currentField): void
    {
        $this->currentField = $currentField;
        $this->settings     = $currentField->settings;

        if (isFieldsKitMultilingualEnabled()) {
            $this->currentLanguage   = config('fields-kit.multilingual.default_language', 'ru');
            $this->multilingualCache = $this->currentField->getRawValue()->toArray();
            if (Arr::isList($this->multilingualCache)) {
                $this->multilingualCache = transpose($this->multilingualCache);
            }

            if (!isset($this->multilingualCache[$this->currentLanguage]) && isset($this->multilingualCache['value'])) {
                $this->multilingualCache = [
                    $this->currentLanguage => $this->multilingualCache['value']
                ];
            }

            $this->value = $currentField->getMLValue($this->currentLanguage);
        } else {
            $this->value = $currentField->getValue();
        }


    }

    /**
     * @return void
     */
    public function save(): void
    {
        if (isFieldsKitMultilingualEnabled()) {
            $this->cacheMultilingualValue();
            $this->currentField->setMLValue($this->multilingualCache);
        } else {
            $this->currentField->setValue($this->value);
        }
    }

    /**
     * @param string|null $lang
     * @return string
     */
    public function getLanguageFlag(?string $lang = null): string
    {
        if (is_null($lang)) {
            $lang = $this->currentLanguage;
        }

        $langSettings = config('fields-kit.multilingual.languages')[$lang] ?? [];
        return $langSettings['flag'] ?? '';
    }

    /**
     * @param string $languageKey
     * @return void
     */
    public function setCurrentLanguage(string $languageKey): void
    {
        $this->cacheMultilingualValue();

        $this->currentLanguage = $languageKey;
        $this->value = $this->multilingualCache[$languageKey] ?? $this->currentField->getEmptyValue();

//        $this->render();
    }

    /**
     * @return void
     */
    protected function cacheMultilingualValue(): void
    {
        if (isset($this->value)) {
            $this->multilingualCache[$this->currentLanguage] = $this->value;
        }
    }

    /**
     * @param bool|null $keysOnly
     * @return array
     */
    public function getLanguages(?bool $keysOnly = false): array
    {
        $languages = config('fields-kit.multilingual.languages', []);
        if ($keysOnly) {
            return array_keys($languages);
        }

        return $languages;
    }
}
