<?php

namespace Batiscaff\FieldsKit\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarFieldData;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class ListType.
 * @package Batiscaff\FieldsKit\Types
 */
class ListType extends AbstractType
{
    public const COLUMNS_MAX_COUNT = 5;
    public const LIST_TYPE_COMMON = 'common';
    public const LIST_TYPE_FLAT   = 'flat';

    /**
     * @return string
     */
    public static function livewireClass(): string
    {
        return \Batiscaff\FieldsKit\Http\Livewire\Types\ListType::class;
    }

    /**
     * @return mixed
     */
    public function getRawValue(): mixed
    {
        return $this->peculiarField->data()->pluck('value');
    }

    /**
     * @return Collection
     */
    public function getValue(): Collection
    {
        return $this->getRawValue();
    }

    /**
     * @return mixed
     */
    public function getEmptyValue(): mixed
    {
        return [];
    }

    /**
     * @param string|null $lang
     * @return mixed
     */
    public function getMLValue(?string $lang = null): mixed
    {
        if (empty($lang)) {
            $lang = config('fields-kit.multilingual.default_language', 'ru');
        }

        $list = $this->getRawValue();
        $value = [];
        foreach ($list as $item) {
            if (isset($item[$lang])) {
                $value[] = $item[$lang];
            }
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getShortValue(): string
    {
        if (isFieldsKitMultilingualEnabled()) {
            $list = $this->getMLValue();
        } else {
            $list = $this->getValue();
        }

        $cnt = count($list);

        return $cnt ? trans_choice('fields-kit::section.items-count', $cnt, ['count' => $cnt])
            : __('fields-kit::section.no-value');
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

                $valueItem->value = $this->normalizeValue($value[$i]);
                $valueItem->save();
            }
        }
    }

    /**
     * @param string $value
     * @return void
     */
    function setMLValue(mixed $value): void
    {
        $data = $this->peculiarField->data ?? [];
        $value = transpose($value);

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

                $normalized = [];
                foreach ($value[$i] as $lang => $langValue) {
                    $normalized[$lang] = $this->normalizeValue($langValue);
                }

                $valueItem->value = $normalized;
                $valueItem->save();
            }
        }
    }

    /**
     * @return mixed
     */
    public function getJson(): mixed
    {
        $result = parent::getJson();

        $settings = $this->getSettings();
        if (isset($settings['list-type']) && $settings['list-type'] == self::LIST_TYPE_FLAT) {
            $result = $result->pluck('value');
        }

        return $result;
    }

    /**
     * @return Collection
     */
    public function getSettings(): Collection
    {
        $settings = collect($this->peculiarField->settings);

        if (!$settings->has('list-type')) {
            $settings['list-type'] = self::LIST_TYPE_COMMON;
        }

        return $settings;
    }

    /**
     * @param Collection $settings
     * @return void
     */
    public function setSettings(Collection $settings): void
    {
        if (!$settings->has('list-type')) {
            $settings['list-type'] = self::LIST_TYPE_COMMON;
        }

        if ($settings['list-type'] === self::LIST_TYPE_COMMON && !$settings->has('columns')) {
            $settings['columns'] = [];
        }

        $this->peculiarField->settings = $settings;
        $this->normalizeAllValues();
    }

    /**
     * @param bool|null $isReverse
     * @return void
     */
    public function convertDataToMultilingual(?bool $isReverse = false): void
    {
        $defaultLang = $this->getDefaultLanguage();
        foreach ($this->peculiarField->data as $data) {
            if ($isReverse) {
                if (isset($data->value[$defaultLang])) {
                    $data->value = $data->value[$defaultLang];
                }
            } else {
                if (!isset($data->value[$defaultLang])) {
                    $data->value = [$defaultLang => $data->value];
                }
            }

            $data->save();
        }
    }

    /**
     * @return void
     */
    protected function normalizeAllValues(): void
    {
        $data = $this->peculiarField->data ?? [];
        foreach ($data as $item) {
            $item->value = $this->normalizeValue($item->value->toArray());
            $item->save();
        }
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function normalizeValue(mixed $value): mixed
    {
        $settings = $this->getSettings();
        if ($settings['list-type'] === self::LIST_TYPE_COMMON) {
            $columnNames = [];
            foreach ($settings['columns'] as $column) {
                if (!isset($value[$column['name']])) {
                    $value[$column['name']] = '';
                }

                $columnNames[] = $column['name'];
            }

            // Remove values for removed columns
            $value = Arr::only($value, $columnNames);
        }

        return $value;
    }
}
