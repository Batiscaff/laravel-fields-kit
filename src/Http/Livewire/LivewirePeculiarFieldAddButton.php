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
    public bool $isFlatGroup = false;

    public array $typesList;

    protected $listeners = ['reRenderFieldData'];

    /**
     * @return void
     */
    public function mount(): void
    {
        $typesConfig = config('fields-kit.types', []);
        $this->typesList = array_keys($typesConfig);

        $this->isFlatGroup = $this->model instanceof \Batiscaff\FieldsKit\Contracts\PeculiarField
            && $this->model->typeInstance instanceof \Batiscaff\FieldsKit\Types\GroupType
            && $this->model->getSettings('group-type') === \Batiscaff\FieldsKit\Types\GroupType::GROUP_TYPE_FLAT
        ;
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
    public function reRenderFieldData(): void
    {
        $this->mount();
        $this->render();
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
        $rules = [
            'newFieldType'  => 'required|string',
            'newFieldTitle' => 'required|string',
        ];

        if (!$this->isFlatGroup) {
            $rules['newFieldName']  = 'required|string|regex:/^[a-z]\w*$/i';
        }

        $this->validate($rules);

        $sort = 1 + (int) $this->model->peculiarFields()->max('sort');

        $field = $this->model->peculiarFields()->make([
            'type'  => $this->newFieldType,
            'name'  => $this->newFieldName,
            'title' => $this->newFieldTitle,
            'sort'  => $sort,
        ]);

        $field->typeInstance->setSettings(collect([]));
        $field->save();

        $this->emit('itemAdded');
        $this->emit(config('fields-kit.flash_key'), [
            'message' => __('fields-kit::messages.field-added', [
                'name' => $this->newFieldTitle
            ]),
            'type' => 'success',
        ]);

        $this->isAddModalOpen = false;
        $this->newFieldType = '';
        $this->newFieldName = '';
        $this->newFieldTitle = '';
    }
}
