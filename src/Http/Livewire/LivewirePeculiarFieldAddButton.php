<?php

namespace Batiscaff\FieldsKit\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Class LivewirePeculiarFieldAddButton.
 * @package Batiscaff\FieldsKit\Http\Livewire
 */
class LivewirePeculiarFieldAddButton extends Component
{
    public $model;

    public string $newFieldType = '';
    public string $newFieldName = '';
    public string $newFieldTitle = '';

    public bool $isAddModalOpen = false;

    public array $typesList;

    /**
     * @return void
     */
    public function mount(): void
    {
        $typesConfig = config('fields-kit.types', []);
        $this->typesList = array_keys($typesConfig);
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::peculiar-fields-add-button');
    }

    /**
     * @return void
     */
    public function addFieldModal(): void
    {
        $this->isAddModalOpen = true;
    }

    /**
     * @return void
     */
    public function addField(): void
    {
        $this->validate([
            'newFieldType'  => 'required|string',
            'newFieldName'  => 'required|string|regex:/^[a-z]\w*$/i',
            'newFieldTitle' => 'required|string',
        ]);

        $sort = 1 + (int) $this->model->peculiarFields()->max('sort');

        $field = $this->model->peculiarFields()->make([
            'type'  => $this->newFieldType,
            'name'  => $this->newFieldName,
            'title' => $this->newFieldTitle,
            'sort'  => $sort,
        ]);

        $field->typeInstance->setSettings(collect([]));

        $field->save();

        $this->isAddModalOpen = false;
        $this->newFieldType = '';
        $this->newFieldName = '';
        $this->newFieldTitle = '';

        $this->emit('itemAdded');
    }
}
