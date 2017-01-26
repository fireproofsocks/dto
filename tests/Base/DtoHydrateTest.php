<?php

namespace DtoTest\Base;

use Dto\Dto;
use Dto\DtoInterface;

class DtoHydrateTest extends DtoTestCase
{

    public function testInstantiation()
    {
        $dto = new Dto(null, null, $this->getMockServiceContainer([]));
        $this->assertInstanceOf(DtoInterface::class, $dto);
    }

    public function testSetAndRetrieveScalar()
    {
        $dto = new Dto(null, null, $this->getMockServiceContainer('kitty', 'scalar'));
        $this->assertEquals('kitty', $dto->toScalar());
    }

    public function testSetAndRetrieveArray()
    {
        $dto_a = new Dto(null, null, $this->getMockServiceContainer('a', 'scalar'));
        $dto_b = new Dto(null, null, $this->getMockServiceContainer('b', 'scalar'));
        $dto_c = new Dto(null, null, $this->getMockServiceContainer('c', 'scalar'));

        $dto = new Dto(null, null, $this->getMockServiceContainer([$dto_a, $dto_b, $dto_c], 'array'));

        $this->assertEquals(['a', 'b', 'c'], $dto->toArray());
    }

    public function testSetAndRetrieveObject()
    {
        $dto_a = new Dto(null, null, $this->getMockServiceContainer('apple', 'scalar'));
        $dto_b = new Dto(null, null, $this->getMockServiceContainer('boy', 'scalar'));

        $dto = new Dto(null, null, $this->getMockServiceContainer(['a' => $dto_a, 'b' => $dto_b], 'object'));
        $this->assertEquals(['a' => 'apple', 'b' => 'boy'], $dto->toArray());
    }

    public function testHydrate()
    {
        $dto = new Dto(null, null, $this->getMockServiceContainer('kitty', 'scalar'));
        $dto->hydrate('overridden-by-regulators-filter-method');
        $this->assertEquals('kitty', $dto->toScalar());
    }
}