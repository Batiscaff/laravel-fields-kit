<?php

namespace Batiscaff\FieldsKit\Types;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Class StringType.
 * @package Batiscaff\FieldsKit\Types
 */
class StringType extends AbstractType
{
    /**
     * @return string
     */
    public static function livewireClass(): string
    {
        return \Batiscaff\FieldsKit\Http\Livewire\Types\StringType::class;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->peculiarField->data[0]?->value['value'] ?? '';
    }

    /**
     * @return string
     */
    public function getShortValue(): string
    {
        return Str::limit(htmlspecialchars($this->getValue()), 50);
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
        if (!$settings->has('multistring')) {
            $settings['multistring'] = false;
        }

        $this->peculiarField->settings = $settings;
    }
}
