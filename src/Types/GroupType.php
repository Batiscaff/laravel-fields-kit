<?php

namespace Batiscaff\FieldsKit\Types;

use Illuminate\Support\Collection;

/**
 * Class GroupType.
 * @package Batiscaff\FieldsKit\Types
 */
class GroupType extends AbstractType
{
    /**
     * @return string
     */
    public static function livewireClass(): string
    {
        return \Batiscaff\FieldsKit\Http\Livewire\Types\GroupType::class;
    }

    /**
     * @return Collection
     */
    public function getValue(): Collection
    {
        return $this->peculiarField->peculiarFields()->orderBy('sort')->get();
    }

    /**
     * @return string
     */
    public function getShortValue(): string
    {
        $list = $this->getValue();
        $cnt = count($list);

        return $cnt ? trans_choice('fields-kit::section.fields-count', $cnt, ['count' => $cnt])
            : __('fields-kit::section.no-fields');
    }

    /**
     * @param string $value
     * @return void
     */
    function setValue(mixed $value): void
    {
    }

    public function getJson(): mixed
    {
        $result = [];
        foreach ($this->peculiarField->peculiarFields as $child) {
            $result[$child['name']] = $child->getJson();
        }

        return $result;
    }

    /**
     * @return Collection
     */
    public function getSettings(): Collection
    {
        $settings = collect($this->peculiarField->settings);

        return $settings;
    }

    /**
     * @param Collection $settings
     * @return void
     */
    public function setSettings(Collection $settings): void
    {
        $this->peculiarField->settings = $settings;
    }
}