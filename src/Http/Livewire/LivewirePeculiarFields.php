<?php

namespace Batiscaff\FieldsKit\Http\Livewire;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Class LivewirePeculiarFields.
 * @package Batiscaff\FieldsKit\Http\Livewire
 */
class LivewirePeculiarFields extends Component
{

    public $model;

    public string $newFieldType = '';
    public string $newFieldName = '';
    public string $newFieldTitle = '';


    public ?int $pfIdForDelete = null;
    public bool $pfIsDeleteConfirmModalOpen = false;
    public bool $pfIsAddModalOpen = false;

    public array $typesList;

    protected $listeners = ['refreshComponent' => '$refresh'];

    /**
     * @return void
     */
    public function mount()
    {
        $typesConfig = config('fields-kit.types', []);
        $this->typesList = array_keys($typesConfig);
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('fields-kit::peculiar-fields-section', [
            'fieldsList' => $this->model->peculiarFields,
        ]);
    }

    /**
     * @return void
     */
    public function pfAddFieldModal(): void
    {
        $this->pfIsAddModalOpen = true;
    }

    /**
     * @param PeculiarField $peculiarFieldClass
     * @return void
     */
    public function pfAddField(PeculiarField $peculiarFieldClass): void
    {
        $this->validate([
            'newFieldType'  => 'required|string',
            'newFieldName'  => 'required|string|regex:/^[a-z]\w*$/i',
            'newFieldTitle' => 'required|string',
        ]);

        $sort = 1 + (int) $this->model->peculiarFields()->max('sort');

        $this->model->peculiarFields()->create([
            'type'  => $this->newFieldType,
            'name'  => $this->newFieldName,
            'title' => $this->newFieldTitle,
            'sort'  => $sort,
        ]);

        $this->pfIsAddModalOpen = false;
        $this->newFieldType = '';
        $this->newFieldName = '';
        $this->newFieldTitle = '';

        $this->emit('refreshComponent');
    }

    /**
     * @param int $fieldId
     * @return void
     */
    public function pfAskToDelete(int $fieldId): void
    {
        $this->pfIdForDelete              = $fieldId;
        $this->pfIsDeleteConfirmModalOpen = true;
    }

    /**
     * @param PeculiarField $peculiarFieldClass
     * @return void
     */
    public function pfDelete(PeculiarField $peculiarFieldClass): void
    {
        if (!empty($this->pfIdForDelete)) {
            $peculiarFieldClass::destroy($this->pfIdForDelete);
            $this->pfIdForDelete = null;
        }

        $this->pfIsDeleteConfirmModalOpen = false;
        $this->emit('refreshComponent');
    }
}
