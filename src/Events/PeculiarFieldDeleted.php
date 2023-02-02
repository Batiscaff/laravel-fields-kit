<?php

namespace Batiscaff\FieldsKit\Events;

use Batiscaff\FieldsKit\Contracts\PeculiarField;

class PeculiarFieldDeleted
{
    public PeculiarField $peculiarField;

    public function __construct(PeculiarField $peculiarField)
    {
        $this->peculiarField = $peculiarField;
    }
}
