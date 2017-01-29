<?php

namespace DtoTest\Base;

use Dto\Dto;
use Dto\DtoInterface;

class DtoHydrateTest extends DtoTestCase
{

    public function testInstantiation()
    {
        $dto = new Dto(null, null, $this->getMockRegulator([]));
        $this->assertInstanceOf(DtoInterface::class, $dto);
    }

    public function testSetAndRetrieveScalar()
    {
        $dto = new Dto(null, null, $this->getMockRegulator('kitty', 'scalar'));
        $this->assertEquals('kitty', $dto->toScalar());
    }

    public function testSetAndRetrieveArray()
    {
        $dto_a = new Dto(null, null, $this->getMockRegulator('a', 'scalar'));
        $dto_b = new Dto(null, null, $this->getMockRegulator('b', 'scalar'));
        $dto_c = new Dto(null, null, $this->getMockRegulator('c', 'scalar'));

        $dto = new Dto(null, null, $this->getMockRegulator(null, 'array', [$dto_a, $dto_b, $dto_c]));

        //$this->assertEquals(['a', 'b', 'c'], $dto->toArray());
    }

//    public function testSetAndRetrieveObject()
//    {
//        $dto_a = new Dto(null, null, $this->getMockRegulator('apple', 'scalar'));
//        $dto_b = new Dto(null, null, $this->getMockRegulator('boy', 'scalar'));
//
//        $dto = new Dto(null, null, $this->getMockRegulator(['a' => $dto_a, 'b' => $dto_b], 'object'));
//        $this->assertEquals(['a' => 'apple', 'b' => 'boy'], $dto->toArray());
//    }
//
//    public function testHydrate()
//    {
//        $dto = new Dto(null, null, $this->getMockRegulator('kitty', 'scalar'));
//        $dto->hydrate('overridden-by-regulators-filter-method');
//        $this->assertEquals('kitty', $dto->toScalar());
//    }
}