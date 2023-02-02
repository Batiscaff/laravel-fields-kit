<?php

namespace Batiscaff\FieldsKit\Events;

use Batiscaff\FieldsKit\Contracts\PeculiarField;

class PeculiarFieldUpdated
{
    public PeculiarField $peculiarField;

    public function __construct(PeculiarField $peculiarField)
    {
        $this->peculiarField = $peculiarField;
    }
}
