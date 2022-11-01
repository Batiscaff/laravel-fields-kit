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
     * @param array $value
     * @return void
     */
    public function setMLValue(array $value): void
    {

    }

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
     * @return mixed
     */
    public function getRawValue(): mixed
    {
        $data = $this->peculiarField->data;
        return isset($data[0]) ? $data[0]->value : null;
    }

    /**
     * @param string|null $lang
     * @return mixed
     */
    public function getMLValue(?string $lang = null): mixed
    {
        if (empty($lang)) {
            $lang = config('fields-kit.multilingual.default_language', 'ru');
        }

        $value = $this->getRawValue();
        if (isset($value[$lang])) {
            return $value[$lang];
        }

        return $value;
    }

    /**
     * @return mixed
     */
    public function getEmptyValue(): mixed
    {
        return null;
    }

    /**
     * @param bool|null $isReverse
     * @return void
     */
    public function convertDataToMultilingual(?bool $isReverse = false): void
    {
        $defaultLang = $this->getDefaultLanguage();
        foreach ($this->peculiarField->data as $data) {
            if ($isReverse) {
                if (isset($data->value[$defaultLang])) {
                    $data->value = ['value' => $data->value[$defaultLang]];
                }
            } else {
                if (isset($data->value['value'])) {
                    $data->value = [$defaultLang => $data->value['value']];
                }
            }

            $data->save();
        }
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

        // Т.к. в плоских списках name не используется, то в этом случае ориентируемся на title
        $attr = $this->peculiarField->name ? 'name' : 'title';

        // Подбираем новое имя, избегая дублирования в рамках текущей или новой модели
        $num = -1;
        do {
            $num++;
            $str = $this->peculiarField->{$attr};
            if ($num > 0) {
                $str .= ($attr === 'name') ? "_copy_{$num}" : __('fields-kit::section.copy-title', ['num' => $num]);
            }

            $exists = $peculiarFieldClass::whereMorphedTo('model', $newModel)
                ->where($attr, '=', $str)
                ->exists()
            ;
        } while ($exists);

        $title = $this->peculiarField->title;
        $name  = $this->peculiarField->name;
        if ($num > 0) {
            $title .= __('fields-kit::section.copy-title', ['num' => $num]);
            if ($name) {
                $name .= "_copy_{$num}";
            }
        }

        return [$name, $title];
    }

    /**
     * @return string
     */
    public function getDefaultLanguage(): string
    {
        return config('fields-kit.multilingual.default_language', 'ru');
    }
}
