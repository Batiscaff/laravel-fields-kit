<?php

namespace Batiscaff\FieldsKit\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarFieldData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\TemporaryUploadedFile;

/**
 * Class ImageType.
 * @package Batiscaff\FieldsKit\Types
 */
class ImageType extends AbstractType
{
    public const STORAGE_DIR = 'peculiar_fields';

    /**
     * @return string
     */
    public static function livewireClass(): string
    {
        return \Batiscaff\FieldsKit\Http\Livewire\Types\ImageType::class;
    }

    /**
     * @return Collection
     */
    public function getValue(): mixed
    {
        return $this->peculiarField->data()->first()?->value;
    }

    /**
     * @param string $value
     * @return void
     */
    function setValue(mixed $value): void
    {
        $data = $this->peculiarField->data()->first();
        $hash = md5('peculiarFields' . $this->peculiarField->id);

        if (empty($value)) {
            if (!empty($data)) {
                $data->delete();
            }
        } else {
            if (empty($data)) {
                $data = app(PeculiarFieldData::class);
                $data->field_id = $this->peculiarField->id;
            }

            if ($value instanceof TemporaryUploadedFile) {
                if (!$data->value->isEmpty()) {
                    Storage::disk('public')->delete($data->value['src']);
                }

                $filePath = $value->store(self::STORAGE_DIR . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2), 'public');
            } else {
                $filePath = $value['src'];
            }

            $data->value = ['src' => $filePath];
            $data->save();
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
     * @param PeculiarFieldData $item
     * @return void
     */
    public function afterDeleteItem(PeculiarFieldData $item): void
    {
        if (!empty($item->value['src'])) {
            Storage::disk('public')->delete($item->value['src']);
        }
    }
}
