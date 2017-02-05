<?php
namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class PolymorphismTest extends TestCase
{
    public function testEmpty()
    {
        $dto = new Dto();

        $this->assertEquals(null, $dto->toScalar());

        $dto->hydrate([]);
        $this->assertEquals([], $dto->toArray());
        $this->assertEquals(new \stdClass(), $dto->toObject());
    }
}