<?php

namespace Batiscaff\FieldsKit\Traits;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

/**
 * Trait HasPeculiarFields.
 * @package Batiscaff\FieldsKit\Traits
 *
 * @property-read Collection $peculiarFields
 */
trait HasPeculiarFields
{
    /**
     * @return MorphMany
     */
    public function peculiarFields(): MorphMany
    {
        return $this->morphMany(app(PeculiarField::class), 'model')->orderBy('sort');
    }

    /**
     * @return string
     */
    public function backwardLink(): string
    {
        return '/' . Str::snake(class_basename(self::class)) . '/' . $this->id;
    }

    /**
     * @return array
     */
    public function peculiarFieldsTree(): array
    {
        $result = [];
        foreach ($this->peculiarFields as $field) {
            $result[$field['name']] = $field->getJson();
        }

        return $result;
    }
}
