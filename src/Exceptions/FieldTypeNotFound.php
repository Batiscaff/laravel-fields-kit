<?php

namespace Batiscaff\FieldsKit\Exceptions;

class FieldTypeNotFound extends \Exception
{
    public function __construct(string $type)
    {
        parent::__construct("Cannot find peculiar field type '{$type}'");
    }
}
