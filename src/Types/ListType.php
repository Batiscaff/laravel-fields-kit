<?php

namespace Batiscaff\FieldsKit\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarFieldData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 * Class ListType.
 * @package Batiscaff\FieldsKit\Types
 */
class ListType extends AbstractType
{
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
        $settings['columns'] = json_encode($settings['columns'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $settings;
    }

    /**
     * @param Collection $settings
     * @return void
     */
    public function setSettings(Collection $settings): void
    {
        $settings['columns'] = json_decode($settings['columns'] ?? '[]', true);
        $this->peculiarField->settings = $settings;
    }
}
