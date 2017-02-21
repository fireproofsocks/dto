<?php
namespace DtoTest\Tutorial;

use Dto\Dto;
use DtoTest\TestCase;

class GettingStartedScalarTest extends TestCase
{
    public function testConstruct()
    {
        $schema = [
            'type' => 'string'
        ];

        $dto = new Dto('some string', $schema);

        $this->assertEquals('some string', $dto->toScalar());

        $dto->hydrate(123);
        $this->assertEquals('123', $dto->toScalar());
    }

    public function testScalarsAsPartOfObject()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'x' => ['type' => 'string'],
                'y' => ['type' => 'integer'],
            ]
        ];

        $dto = new Dto(null, $schema);

        $dto->x = 'a string';
        $dto->y = 456;

        $this->assertEquals(['x' => 'a string', 'y' => 456], $dto->toArray());
    }
}