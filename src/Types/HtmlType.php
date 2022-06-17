<?php

namespace Batiscaff\FieldsKit\Types;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 * Class HtmlType.
 * @package Batiscaff\FieldsKit\Types
 */
class HtmlType extends AbstractType
{
    /**
     * @return string
     */
    public static function livewireClass(): string
    {
        return \Batiscaff\FieldsKit\Http\Livewire\Types\HtmlType::class;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->peculiarField->data[0]?->value['value'] ?? '';
    }

    /**
     * @param string $value
     * @return void
     */
    function setValue(mixed $value): void
    {
        $data = $this->peculiarField->data[0] ?? App::make(config('fields-kit.models.peculiar_fields_data'));
        $data->value['value'] = $value;

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
