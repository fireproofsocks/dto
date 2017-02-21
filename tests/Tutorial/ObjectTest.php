<?php
namespace DtoTest\Tutorial;

use Dto\Dto;
use DtoTest\TestCase;

class ObjectTest extends TestCase
{
    /**
     * @expectedException \Dto\Exceptions\InvalidObjectValueException
     */
    public function testMaxProperties()
    {
        $schema = [
            'type' => 'object',
            'maxProperties' => 3,
        ];

        $dto = new Dto(null, $schema);

        $dto->a = 'apple';
        $dto->b = 'boy';
        $dto->c = 'cat';
        $dto->d = 'BOOM';
    }
}