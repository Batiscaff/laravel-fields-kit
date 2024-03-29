<?php

namespace Batiscaff\FieldsKit\Models;

use Batiscaff\FieldsKit\Contracts\PeculiarFieldData as PeculiarFieldDataContract;
use Batiscaff\FieldsKit\Events\PeculiarFieldUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * Class PeculiarFieldData.
 * @package Batiscaff\FieldsKit\Models
 *
 * @property int $id
 * @property int $field_id
 * @property Collection $value
 * @property int $sort
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PeculiarFieldData extends Model implements PeculiarFieldDataContract
{
    protected $fillable = ['value', 'sort'];

    protected $attributes = [
        'value' => '{}',
        'sort'  => 0,
    ];

    protected $casts = [
        'value' => AsCollection::class,
    ];

    /**
     * @inheritDoc
     */
    public function getTable()
    {
        return config('fields-kit.table_names.peculiar_fields_data');
    }

    /**
     * @inheritdoc
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function($createdField) {
            event(new PeculiarFieldUpdated($createdField->peculiarField));
        });

        static::updated(function($updatedField) {
            event(new PeculiarFieldUpdated($updatedField->peculiarField));
        });

        static::deleted(function ($item) {
            $typeInstance = $item->peculiarField->typeInstance;

            if (method_exists($typeInstance, 'afterDeleteItem')) {
                $typeInstance->afterDeleteItem($item);
            }

            event(new PeculiarFieldUpdated($item->peculiarField));
        });
    }

    /**
     * @return BelongsTo
     */
    public function peculiarField(): BelongsTo
    {
        return $this->belongsTo(app(\Batiscaff\FieldsKit\Contracts\PeculiarField::class), 'field_id');
    }
}
