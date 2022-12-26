<?php

namespace Batiscaff\FieldsKit\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Batiscaff\FieldsKit\Contracts\PeculiarFieldData;
use Buglinjo\LaravelWebp\Facades\Webp;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Filesystem\Filesystem;
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
     * @return string
     */
    public function getShortValue(): string
    {
        $image = $this->getValue();

        return $image ? trans_choice('fields-kit::section.images-count', 1, ['count' => 1])
            : __('fields-kit::section.no-value');
    }

    /**
     * @param string $value
     * @return void
     */
    function setValue(mixed $value): void
    {
        $data = $this->peculiarField->data()->first();

        if (empty($value)) {
            if (!empty($data)) {
                $data->delete();
            }
        } else {
            if (empty($data)) {
                $data = app(PeculiarFieldData::class);
                $data->field_id = $this->peculiarField->id;
            }

            $newValue = [];

            if ($value instanceof TemporaryUploadedFile) {
                if (!$data->value->isEmpty()) {
                    Storage::disk('public')->delete($data->value['src']);
                }

                $hash = md5('peculiarFields' . $this->peculiarField->id);
                $path = self::STORAGE_DIR . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2);

                $newValue['src'] = $value->store($path, 'public');

                $webp = Webp::make($value);
                if ($webp && $value->getMimeType() !== 'image/svg+xml') {
                    $webpPath = $path . '/' . pathinfo($newValue['src'], PATHINFO_FILENAME) . '.webp';
                    $webp->save(storage_path('app/public/' . $webpPath));
                    $newValue['srcWebp'] = $webpPath;
                }

            } else {
                $newValue = $value;
            }

            $data->value = $newValue;
            $data->save();
        }
    }

    /**
     * @return mixed
     */
    public function getJson(): mixed
    {
        $json = $this->getValue();
        $json['src'] = !empty($json['src']) ? Storage::disk('public')->url($json['src']) : null;
        if (!empty($json['srcWebp'])) {
            $json['srcWebp'] = Storage::disk('public')->url($json['srcWebp']);
        }

        return $json;
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
            if (!empty($item->value['srcWebp'])) {
                Storage::disk('public')->delete($item->value['srcWebp']);
            }
        }
    }

    /**
     * @param PeculiarField $newField
     * @return void
     * @throws BindingResolutionException
     */
    protected function copyDataTo(PeculiarField $newField): void
    {
        $filesystem = app()->make(Filesystem::class);
        $dataItem = $this->peculiarField->data()->first();

        if ($dataItem) {
            $hash = md5('peculiarFields' . $newField->id);
            $newPath = self::STORAGE_DIR . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2);

            // Копируем значение и прикрепляем к новому полю
            $newDataItem = $dataItem->replicate()
                ->peculiarField()
                ->associate($newField)
            ;

            $newValue['src'] = $newPath . '/' . $filesystem->basename($newDataItem->value['src']);

            // Копируем файл
            Storage::disk('public')->copy(
                $newDataItem->value['src'],
                $newValue['src']
            );

            if (!empty($newDataItem->value['srcWebp'])) {
                $newValue['srcWebp'] = $newPath . '/' . $filesystem->basename($newDataItem->value['srcWebp']);

                // Копируем файл webp
                Storage::disk('public')->copy(
                    $newDataItem->value['srcWebp'],
                    $newValue['srcWebp']
                );
            }

            $newDataItem->value = $newValue;
            $newDataItem->save();
        }
    }
}
