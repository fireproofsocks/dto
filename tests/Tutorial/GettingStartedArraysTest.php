<?php
namespace DtoTest\Tutorial;

use Dto\Dto;
use DtoTest\TestCase;

class GettingStartedArraysTest extends TestCase
{
    public function testDefiningArrayOfStrings()
    {
        $data = ['ape', 'bat', 'cat'];

        $schema = [
            'type' => 'array',
            'items' => ['type' => 'string']
        ];

        $dto = new Dto($data, $schema);

        $this->assertEquals($dto[0], 'ape');
        $this->assertEquals($dto[1], 'bat');
        $this->assertEquals($dto[2], 'cat');
    }

    public function testValuesAreCastToString()
    {
        $schema = [
            'type' => 'array',
            'items' => ['type' => 'string']
        ];

        $dto = new Dto([], $schema);

        $dto[] = 123;
        $dto[] = true;

        $this->assertEquals(['123', '1'], $dto->toArray());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testObjectPropertiesAreNotAllowed()
    {
        $schema = [
            'type' => 'array',
            'items' => ['type' => 'string']
        ];

        $dto = new Dto([], $schema);

        $dto->whaaat = 'Nice try.';
    }
}