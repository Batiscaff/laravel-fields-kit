<?php

namespace Batiscaff\FieldsKit\Types;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
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
}
