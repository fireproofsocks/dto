<?php

namespace DtoTest\SimpleObject;

use Dto\Dto;

class SimpleObjectDto extends Dto
{
    protected $properties = [
        'my_string' => ['type' => 'string'],
        'my_integer' => ['type' => 'integer'],
        'my_number' => ['type' => 'number'],
        'my_boolean' => ['type' => 'boolean'],
    ];
}