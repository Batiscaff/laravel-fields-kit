<?php

namespace Batiscaff\FieldsKit\Models;

use Batiscaff\FieldsKit\Contracts\PeculiarField as PeculiarFieldContract;
use Batiscaff\FieldsKit\Exceptions\FieldTypeNotFound;
use Batiscaff\FieldsKit\Types\AbstractType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as DbCollection;
use Illuminate\Translation\Translator;

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
 * @property-read string $typeAsString
 * @property-read AbstractType $typeInstance
 * @property-read DbCollection $peculiarFields
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
     * @return MorphMany
     */
    public function peculiarFields(): MorphMany
    {
        return $this->morphMany(static::class, 'model');
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

    /**
     * @return string
     */
    public function getTypeAsStringAttribute(): string
    {
        /** @var Translator $translator */
        $translator = app('translator');
        $key = "fields-kit::types.{$this->type}";
        return $translator->has($key) ? $translator->get($key) : $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->typeInstance->getValue();
    }

    /**
     * @return string
     */
    public function getShortValue(): mixed
    {
        if (!method_exists($this->typeInstance, 'getShortValue')) {
            return '';
        }

        return $this->typeInstance->getShortValue();
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setValue(mixed $value): void
    {
        $this->typeInstance->setValue($value);
    }

    /**
     * @return mixed
     */
    public function getJson(): mixed
    {
        return $this->typeInstance->getJson();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getSettings(string $key): mixed
    {
        return Arr::get($this->settings, $key);
    }

    /**
     * @return string
     */
    public function backwardLink(): string
    {
        $model = $this->model;
        if ($model instanceof self) {
            return route('fields-kit.peculiar-field-edit', [
                'currentField' => $model->id
            ]);
        } else {
            return method_exists($model, 'backwardLink') ? $model->backwardLink() : '';
        }
    }
}
