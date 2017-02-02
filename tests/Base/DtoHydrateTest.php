<?php

namespace DtoTest\Base;

use Dto\Dto;
use Dto\DtoInterface;
use Dto\RegulatorInterface;

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

    public function testHydrateAssociativeArray()
    {
        $dto = new Dto(null, null, $this->getMockRegulator(['foo' => 'bar'], 'object'));
        $this->assertEquals(['foo' => 'bar'], $dto->toArray());
    }

    // this fails because we are trying to reuse the same mock for the hydrated Children Dtos,
    // so it gets stuck in trying to write everything as arrays
//    public function testSetAndRetrieveArray()
//    {
//        $dto_a = new PartialMockScalarDto('a');
//        $dto_b = new PartialMockScalarDto('b');
//        $dto_c = new PartialMockScalarDto('c');
//
//        $dto = new Dto([], null, $this->getMockRegulator([$dto_a, $dto_b, $dto_c], 'array'));
//
//        //$this->assertEquals(['a', 'b', 'c'], $dto->toArray());
//    }

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

class PartialMockScalarDto extends Dto
{
    protected $value;

    public function __construct($input = null, $schema = null, $regulator = null)
    {
        // ssh... don't call the parent
        $this->value = $input;
    }

    public function isScalar()
    {
        return true;
    }

    public function toScalar()
    {
        return $this->value;
    }
}
