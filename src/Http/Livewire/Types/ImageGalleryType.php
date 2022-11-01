<?php

namespace Batiscaff\FieldsKit\Http\Livewire\Types;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\WithFileUploads;

/**
 * Class ImageGalleryType.
 * @package Batiscaff\FieldsKit\Http\Livewire\Types
 */
class ImageGalleryType extends AbstractType
{
    use WithFileUploads;

    protected $listeners = ['save', 'reRenderFieldData', 'itemsIsSorted', 'setCurrentLanguage'];

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::types.image_gallery-type');
    }

    /**
     * @return void
     */
    public function save(): void
    {
//        $this->currentField->setValue(new Collection($this->value));
        parent::save();
        $this->reRenderFieldData();
    }

    /**
     * @return void
     */
    public function reRenderFieldData(): void
    {
        $this->mount($this->currentField);
        $this->render();
    }

    /**
     * @return void
     */
    public function addItem(): void
    {
        $this->value[] = [];
    }

    /**
     * @param int $key
     * @return void
     */
    public function removeItem(int $key): void
    {
        unset($this->value[$key]);
        $this->value = array_values($this->value);
    }

    /**
     * @param array $keys
     * @return void
     */
    public function itemsIsSorted(array $keys): void
    {
        $keys = array_flip($keys);
        $values = $this->value;

        uksort($values, function ($a, $b) use ($keys) {
            return $keys[$a] <=> $keys[$b];
        });

        $this->value = array_values($values);
    }

    /**
     * Add new images instead of replacing old images
     *
     * @param mixed $value
     * @return void
     */
    public function updatingValue(mixed &$value): void
    {
        $value = array_merge($this->value, $value);
    }
}
