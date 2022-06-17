<?php

namespace Batiscaff\FieldsKit\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarFieldData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Livewire\TemporaryUploadedFile;

/**
 * Class ImageGalleryType.
 * @package Batiscaff\FieldsKit\Types
 */
class ImageGalleryType extends AbstractType
{
    public const STORAGE_DIR = 'peculiar_fields';

    /**
     * @return string
     */
    public static function livewireClass(): string
    {
        return \Batiscaff\FieldsKit\Http\Livewire\Types\ImageGalleryType::class;
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

        $hash = md5('peculiarFields' . $this->peculiarField->id);

        for ($i=0; $i < $cnt; $i++) {
            if (!isset($value[$i]) || self::isEmptyImagesList($value[$i])) {
                Storage::disk('public')->delete($value[$i]['src']);
                $data[$i]->delete();
            } else {
                if (empty($value[$i])) {
                    continue;
                }

                if (isset($data[$i])) {
                    $valueItem = $data[$i];
                } else {
                    $valueItem = app(PeculiarFieldData::class);
                    $valueItem->field_id = $this->peculiarField->id;
                }

                $dbValue = null;
                if (isset($data[$i])) {
                    $dbValue = $data[$i]->value;
                }

                if ($value[$i] instanceof TemporaryUploadedFile) {
                    if ($dbValue) {
                        Storage::disk('public')->delete($dbValue['src']);
                    }

                    $filePath = $value[$i]->store(self::STORAGE_DIR . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2), 'public');
                } elseif (!empty($value[$i])) {
                    $filePath = $value[$i]['src'];
                } elseif($data[$i]) {
                    Storage::disk('public')->delete($data[$i]['src']);
                    $filePath = null;
                } else {
                    return;
                }

                $valueItem->value = ['src' => $filePath];
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

    /**
     * @param mixed $list
     * @return bool
     */
    protected static function isEmptyImagesList(mixed $list): bool
    {
        if (!is_array($list)) {
            return false;
        }

        foreach ($list as $item) {
            if (!empty($item) && (!is_array($item) || !empty($item['src']))) {
                return false;
            }
        }

        return true;
    }
}
