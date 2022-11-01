<?php

namespace Batiscaff\FieldsKit\Traits;

/**
 * Trait Multilingual.
 * @package Batiscaff\FieldsKit\Traits
 */
trait Multilingual
{
    /**
     * @var string|null
     */
    public ?string $currentLanguage;

    /**
     * @var array
     */
    protected array $multilingualCache;

    /**
     * @return void
     */
    public function multilingualInit(): void
    {
        $this->currentLanguage = config('fields-kit.multilingual.default_language', 'ru');
//        $this->multilingualCache = $this->

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
        $this->currentLanguage = $languageKey;
//        $this->render();
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function preGetValue(mixed $value): mixed
    {
        if (!isFieldsKitMultilingualEnabled()) {
            return $value;
        }

        $languages = $this->getLanguages();
        if (array_intersect_key($value, $languages)) {
            return $value[$this->currentLanguage] ?? null;
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
