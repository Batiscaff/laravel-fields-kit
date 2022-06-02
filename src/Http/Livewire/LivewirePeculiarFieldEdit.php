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

    protected $listeners = ['refreshComponent' => '$refresh'];

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

}
