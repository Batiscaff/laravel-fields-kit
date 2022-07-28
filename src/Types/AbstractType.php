<?php

namespace Batiscaff\FieldsKit\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class AbstractType.
 * @package Batiscaff\FieldsKit\Types
 */
abstract class AbstractType
{
    protected PeculiarField $peculiarField;

    /**
     * @param PeculiarField $peculiarField
     */
    public function __construct(PeculiarField $peculiarField)
    {
        $this->peculiarField = $peculiarField;
    }

    /**
     * @return mixed
     */
    abstract public function getValue(): mixed;

    /**
     * @param mixed $value
     * @return void
     */
    abstract public function setValue(mixed $value): void;

    /**
     * @return Collection
     */
    abstract public function getSettings(): Collection;

    /**
     * @param Collection $settings
     * @return void
     */
    abstract public function setSettings(Collection $settings): void;

    /**
     * @return string
     */
    abstract static function livewireClass(): string;

    /**
     * @return mixed
     */
    public function getJson(): mixed
    {
        return $this->getValue();
    }

    /**
     * @param Model|null $newModel
     * @return PeculiarField
     */
    public function createCopy(?Model $newModel = null): PeculiarField
    {
        // Если новая модель не задана, значит создаём копию в рамках текущей модели
        if (!$newModel) {
            $newModel = $this->peculiarField->model;
        }

        $peculiarFieldClass = app(PeculiarField::class);
        [$name, $title] = $this->getCopyNameAndTitle($newModel);
        $sort = $this->peculiarField->sort;

        // Если копируем в текущую модель, то копия должна идти сразу после оригинала
        if ($newModel === $this->peculiarField->model) {
            // Проверяем, занято ли следующее значение в порядке сортировки элементов
            $nextSortExists = $peculiarFieldClass::whereMorphedTo('model', $newModel)
                ->where('sort', '=', $sort + 1)
                ->exists()
            ;

            // Если следующее значение занято, то сдвигаем сортировку всех последующих элементов на 1
            if ($nextSortExists) {
                $peculiarFieldClass::whereMorphedTo('model', $newModel)
                    ->where('sort', '>', $sort)
                    ->increment('sort')
                ;
            }

            $sort++;
        }

        // Создаём копию поля
        /** @var PeculiarField $newField */
        $newField = $this->peculiarField->replicate()
            ->fill([
                'name'  => $name,
                'title' => $title,
                'sort'  => $sort,
            ])
            ->model()
            ->associate($newModel)
        ;

        $newField->save();

        // Копируем значение(я) поля
        $this->copyDataTo($newField);

        return $newField;
    }

    /**
     * @param PeculiarField $newField
     * @return void
     */
    protected function copyDataTo(PeculiarField $newField): void
    {
        foreach ($this->peculiarField->data as $dataItem) {
            $dataItem->replicate()     // создаём копию
                ->peculiarField()
                ->associate($newField) // меняем принадлежность на новое поле
                ->save()
            ;
        }
    }

    /**
     * @return string[]
     */
    protected function getCopyNameAndTitle(Model $newModel): array
    {
        $peculiarFieldClass = app(PeculiarField::class);

        // Подбираем новое имя, избегая дублирования в рамках текущей или новой модели
        $num = -1;
        do {
            $num++;
            $name = $this->peculiarField->name;
            if ($num > 0) {
                $name .= "_copy_{$num}";
            }

            $exists = $peculiarFieldClass::whereMorphedTo('model', $newModel)
                ->where('name', '=', $name)
                ->exists()
            ;
        } while ($exists);

        $title = $this->peculiarField->title;
        if ($num > 0) {
            $title .= __('fields-kit::section.copy-title', ['num' => $num]);
        }

        return [$name, $title];
    }
}
