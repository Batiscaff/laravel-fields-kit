<?php

namespace Batiscaff\FieldsKit\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarFieldData;
use Illuminate\Support\Collection;

/**
 * Class ListType.
 * @package Batiscaff\FieldsKit\Types
 */
class ListType extends AbstractType
{
    public const COLUMNS_MAX_COUNT = 5;

    /**
     * @return string
     */
    public static function livewireClass(): string
    {
        return \Batiscaff\FieldsKit\Http\Livewire\Types\ListType::class;
    }

    /**
     * @return Collection
     */
    public function getValue(): Collection
    {
        return $this->peculiarField->data()->pluck('value');
    }

    /**
     * @param string $value
     * @return void
     */
    function setValue(mixed $value): void
    {
        $data = $this->peculiarField->data ?? [];
        $cnt = max(count($data), count($value));

        for ($i=0; $i < $cnt; $i++) {
            if (!isset($value[$i])) {
                $data[$i]->delete();
            } else {
                if (isset($data[$i])) {
                    $valueItem = $data[$i];
                } else {
                    $valueItem = app(PeculiarFieldData::class);
                    $valueItem->field_id = $this->peculiarField->id;
                }

                $valueItem->value = $value[$i];
                $valueItem->save();
            }
        }
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
