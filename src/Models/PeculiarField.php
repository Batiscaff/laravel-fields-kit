<?php

namespace Batiscaff\FieldsKit\Models;

use Batiscaff\FieldsKit\Contracts\PeculiarField as PeculiarFieldContract;
use Batiscaff\FieldsKit\Exceptions\FieldTypeNotFound;
use Batiscaff\FieldsKit\Types\AbstractType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

/**
 * Class PeculiarField.
 * @package Batiscaff\FieldsKit\Models
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $type
 * @property string $name
 * @property string $title
 * @property Collection $settings
 * @property int $sort
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Model $model
 * @property-read AbstractType $typeInstance
 */
class PeculiarField extends Model implements PeculiarFieldContract
{
    protected $fillable = ['type', 'name', 'title', 'settings', 'sort'];

    protected $attributes = [
        'settings' => '{}',
    ];

    protected $casts = [
        'settings' => AsCollection::class,
    ];

    /**
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany
     */
    public function data(): HasMany
    {
        return $this->hasMany(app(\Batiscaff\FieldsKit\Contracts\PeculiarFieldData::class), 'field_id');
    }

    /**
     * @return AbstractType
     * @throws FieldTypeNotFound
     */
    public function getTypeInstanceAttribute(): AbstractType
    {
        $typeConfig = config('fields-kit.types.' . $this->type);
        if (!empty($typeConfig) && class_exists($typeConfig)) {
            return new $typeConfig($this);
        } else {
            throw new FieldTypeNotFound($this->type);
        }
    }

    public function getValue()
    {
        return $this->typeInstance->getValue();
    }

    public function setValue($value)
    {
        $this->typeInstance->setValue($value);
    }
}
