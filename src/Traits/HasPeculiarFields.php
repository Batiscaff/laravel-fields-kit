<?php

namespace Batiscaff\FieldsKit\Traits;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasPeculiarFields.
 * @package Batiscaff\FieldsKit\Traits
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
}
