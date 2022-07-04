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

    public bool $isEditModalOpen  = false;
    public bool $isModelFlatGroup = false;

    public Collection $settings;

    protected $listeners = ['refreshComponent' => '$refresh', 'columnsIsSorted', 'reRenderFieldData'];

    /**
     * @param PeculiarField $currentField
     * @return void
     */
    public function mount(PeculiarField $currentField): void
    {
        // Dispatch flash-event if session contains flash-message
        if (session()->has(config('fields-kit.flash_key'))) {
            $this->emit(config('fields-kit.flash_key'), session(config('fields-kit.flash_key')));
        }

        $this->currentField = $currentField;

        $this->type  = $currentField->type;
        $this->name  = $currentField->name;
        $this->title = $currentField->title;

        $this->settings = $currentField->typeInstance->getSettings();

        $this->isModelFlatGroup = $currentField->model instanceof \Batiscaff\FieldsKit\Contracts\PeculiarField
            && $currentField->model->typeInstance instanceof \Batiscaff\FieldsKit\Types\GroupType
            && $currentField->model->getSettings('group-type') === \Batiscaff\FieldsKit\Types\GroupType::GROUP_TYPE_FLAT
        ;
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::peculiar-fields-edit');
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
        return $this->currentField->typeInstance->settingsView
            ?? "fields-kit::types.{$this->type}-type-settings";
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

        $flash = [
            'message' => __('fields-kit::messages.field-updated', [
                'name' => $this->currentField->title
            ]),
            'type' => 'success',
        ];

        if ($isBackward) {
            session()->flash(config('fields-kit.flash_key'), $flash);
            $this->redirect($this->currentField->backwardLink());
        } else {
            $this->emit(config('fields-kit.flash_key'), $flash);
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
