<?php
namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class ReferenceTest extends TestCase
{
    public function testResolveReferencesInSubSchemas()
    {
        $dto = new Dto(null, [
            'type' => 'object',
            'properties' => [
                's' => ['$ref' => __DIR__ . '/data/string.json'],
                'i' => ['$ref' => __DIR__ . '/data/integer.json'],
            ]
        ]);

        $dto->i = '42asdf4';
        $this->assertEquals(42, $dto->i->toScalar());
    }
}