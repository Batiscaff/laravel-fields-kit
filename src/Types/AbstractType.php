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
    abstract function getValue(): mixed;

    /**
     * @param mixed $value
     * @return void
     */
    abstract function setValue(mixed $value): void;

    /**
     * @return Collection
     */
    abstract function getSettings(): Collection;

    /**
     * @param Collection $settings
     * @return void
     */
    abstract function setSettings(Collection $settings): void;

    /**
     * @return string
     */
    abstract static function livewireClass(): string;
}
