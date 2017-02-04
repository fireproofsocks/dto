<?php
namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class ObjectsTest extends TestCase
{
    public function testX()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'b' => ['type' => 'string']
            ],
            'additionalProperties' => false

        ];

        //$dto = new Dto(null, $schema);
        //$dto->hydrate(['a' => 'apple', 'b' => 'banjo']);
        $dto = new Dto(['a' => 'apple', 'b' => 'banjo'], $schema);
        //$dto->hydrate(['a' => 'apple', 'b' => 'banjo']);


        $this->assertEquals(['a' => 'apple', 'b' => 'banjo'], $dto->toArray());
    }
}