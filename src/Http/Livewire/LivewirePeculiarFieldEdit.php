<?php

namespace Batiscaff\FieldsKit\Http\Livewire;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * Class LivewirePeculiarFieldEdit.
 * @package Batiscaff\FieldsKit\Http\Livewire
 *
 * @property-read string $livewireComponent
 * @property-read string $settingsView
 */
class LivewirePeculiarFieldEdit extends Component
{
    public PeculiarField $currentField;

    public string $type = '';
    public string $name = '';
    public string $title = '';
    public string $newName = '';
    public string $newTitle = '';

    public bool $isEditModalOpen = false;

    public Collection $settings;

    protected $listeners = ['refreshComponent' => '$refresh', 'columnsIsSorted'];

    /**
     * @param PeculiarField $currentField
     * @return void
     */
    public function mount(PeculiarField $currentField): void
    {
        $this->currentField = $currentField;

        $this->type  = $currentField->type;
        $this->name  = $currentField->name;
        $this->title = $currentField->title;

        $this->settings = $currentField->typeInstance->getSettings();
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::peculiar-fields-edit');
    }

    /**
     * @return string
     */
    public function getLivewireComponentProperty(): string
    {
        return 'fields-kit-' . $this->type;
    }

    /**
     * @return string
     */
    public function getSettingsViewProperty(): string
    {
        return "fields-kit::types.{$this->type}-type-settings";
    }

    /**
     * @return void
     */
    public function editField(): void
    {
        $this->newName  = $this->currentField->name;
        $this->newTitle = $this->currentField->title;
        $this->settings = $this->currentField->typeInstance->getSettings();
        $this->isEditModalOpen = true;
    }

    /**
     * @return void
     */
    public function updateField(): void
    {
        $this->currentField->name  = $this->newName;
        $this->currentField->title = $this->newTitle;
        $this->currentField->typeInstance->setSettings($this->settings);
        $this->currentField->save();

        $this->isEditModalOpen = false;

        $this->emit('reRenderFieldData');
    }

    /**
     * @param bool|null $isBackward
     * @return void
     */
    public function saveData(?bool $isBackward = false): void
    {
        $this->emitTo($this->getLivewireComponentProperty(), 'save');

        if ($isBackward) {
            $this->redirect($this->currentField->backwardLink());
        } else {
            $this->emit('dataSaved');
        }
    }

    /**
     * @param array $keys
     * @return void
     */
    public function columnsIsSorted(array $keys): void
    {
        $keys    = array_flip($keys);
        $columns = collect($this->settings['columns'] ?? []);

        $this->settings['columns'] = $columns->sortBy(function ($val, $key) use ($keys) {
            return $keys[$key];
        })->values();
    }

    /**
     * @return void
     */
    public function addColumn(): void
    {
        $columns = collect($this->settings['columns'] ?? []);

        $columns[] = [
            'type' => 'string'
        ];

        $this->settings['columns'] = $columns;
    }

    /**
     * @param int $key
     * @return void
     */
    public function removeColumn(int $key): void
    {
        $columns = collect($this->settings['columns'] ?? []);

        if (count($columns) > 1) {
            unset($columns[$key]);
            $columns = $columns->forget($key)->values();
        } else {
            $columns = $columns->map(function ($item) {
                data_set($item, '*', '');
                return $item;
            });
        }

        $this->settings['columns'] = $columns;
    }
}
