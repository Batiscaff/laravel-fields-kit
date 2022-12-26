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
     * @return string
     */
    public function getShortValue(): string
    {
        $list = $this->getValue();
        $cnt = count($list);

        return $cnt ? trans_choice('fields-kit::section.images-count', $cnt, ['count' => $cnt])
            : __('fields-kit::section.no-value');
    }

    /**
     * @param string $value
     * @return void
     */
    function setValue(mixed $value): void
    {
        $data = $this->peculiarField->data ?? [];
        $cnt  = max(count($data), count($value));
        $hash = md5('peculiarFields' . $this->peculiarField->id);
        $path = self::STORAGE_DIR . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2);

        $counted = [];
        foreach ($data as $item) {
            if (!empty($item->value['src'])) {
                $counted[$item->value['src']] = 1;
            }

            if (!empty($item->value['srcWebp'])) {
                $counted[$item->value['srcWebp']] = 1;
            }
        }

        for ($i=0; $i < $cnt; $i++) {
            if (!isset($value[$i]) || self::isEmptyImagesList($value[$i])) {
                self::counterInc($counted, $data[$i]->value['src'], -1);
                if (!empty($data[$i]->value['srcWebp'])) {
                    self::counterInc($counted, $data[$i]->value['srcWebp'], -1);
                }

                app(PeculiarFieldData::class)::withoutEvents(function () use ($data, $i) {
                    $data[$i]->delete();
                });
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

                $newValue = [];

                if ($value[$i] instanceof TemporaryUploadedFile) {
                    if ($dbValue) {
                        self::counterInc($counted, $dbValue['src'], -1);
                        if (!empty($dbValue['srcWebp'])) {
                            self::counterInc($counted, $dbValue['srcWebp'], -1);
                        }
                    }

                    $newValue['src'] = $value[$i]->store($path, 'public');
                    self::counterInc($counted, $newValue['src']);

                    $webp = Webp::make($value[$i]);
                    if ($webp) {
                        $webpPath = $path . '/' . pathinfo($newValue['src'], PATHINFO_FILENAME) . '.webp';
                        $webp->save(storage_path('app/public/' . $webpPath));
                        $newValue['srcWebp'] = $webpPath;
                        self::counterInc($counted, $newValue['srcWebp']);
                    }

                } elseif (!empty($value[$i])) {
                    $newValue = $value[$i];
                    if (!$dbValue || $newValue['src'] != $dbValue['src']) {
                        self::counterInc($counted, $newValue['src']);
                        if (!empty($newValue['srcWebp'])) {
                            self::counterInc($counted, $newValue['srcWebp']);
                        }

                        self::counterInc($counted, $dbValue['src'] ?? null, -1);
                        if (!empty($dbValue['srcWebp'])) {
                            self::counterInc($counted, $dbValue['srcWebp'] ?? null, -1);
                        }
                    }
                } elseif($data[$i]) {
                    self::counterInc($counted, $data[$i]['src'], -1);
                    $newValue['src'] = null;

                    if (!empty($data[$i]['srcWebp'])) {
                        self::counterInc($counted, $data[$i]['srcWebp'], -1);
                        $newValue['srcWebp'] = null;
                    }
                } else {
                    return;
                }

                $valueItem->value = $newValue;
                $valueItem->save();
            }
        }

        foreach ($counted as $path => $count) {
            if (!$count) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getJson(): mixed
    {
        $json = $this->getValue();
        foreach ($json as &$item) {
            $item['src'] = Storage::disk('public')->url($item['src']);
            if (!empty($item['srcWebp'])) {
                $item['srcWebp'] = Storage::disk('public')->url($item['srcWebp']);
            }
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
        foreach ($this->peculiarField->data as $dataItem) {
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

    /**
     * @param Collection|array $counter
     * @param mixed $key
     * @param int|null $value
     * @return void
     */
    protected static function counterInc(Collection|array &$counter, mixed $key, ?int $value = 1): void
    {
        if (is_null($key)) {
            return;
        }

        if (!isset($counter[$key])) {
            $counter[$key] = 0;
        }

        $counter[$key] += $value;
    }
}
