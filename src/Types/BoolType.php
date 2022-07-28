<?php

namespace Batiscaff\FieldsKit\Types;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 * Class BoolType.
 * @package Batiscaff\FieldsKit\Types
 */
class BoolType extends AbstractType
{
    /**
     * @return string
     */
    public static function livewireClass(): string
    {
        return \Batiscaff\FieldsKit\Http\Livewire\Types\BoolType::class;
    }

    /**
     * @return bool
     */
    public function getValue(): bool
    {
        $data = $this->peculiarField->data()->first();
        return !empty($data->value['value']);
    }

    /**
     * @return string
     */
    public function getShortValue(): string
    {
        return $this->getValue() ? __('fields-kit::section.yes') : __('fields-kit::section.no');
    }

    /**
     * @param string $value
     * @return void
     */
    function setValue(mixed $value): void
    {
        $data = $this->peculiarField->data[0] ?? App::make(config('fields-kit.models.peculiar_fields_data'));
        $data->value['value'] = !empty($value);

        $this->peculiarField->data()->save($data);
    }

    /**
     * @return Collection
     */
    public function getSettings(): Collection
    {
        return collect($this->peculiarField->settings);
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
