<?php

return [
    'models' => [
        'peculiar_fields'      => Batiscaff\FieldsKit\Models\PeculiarField::class,
        'peculiar_fields_data' => Batiscaff\FieldsKit\Models\PeculiarFieldData::class,
    ],
    'table_names' => [
        'peculiar_fields'      => 'peculiar_fields',
        'peculiar_fields_data' => 'peculiar_fields_data',
    ],
    'types' => [
        'string'        => Batiscaff\FieldsKit\Types\StringType::class,
        'html'          => Batiscaff\FieldsKit\Types\HtmlType::class,
        'list'          => Batiscaff\FieldsKit\Types\ListType::class,
        'image'         => Batiscaff\FieldsKit\Types\ImageType::class,
        'image_gallery' => Batiscaff\FieldsKit\Types\ImageGalleryType::class,
        'group'         => Batiscaff\FieldsKit\Types\GroupType::class,
    ],
];
