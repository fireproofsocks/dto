<?php

namespace DtoTest\Hydrate;

use DtoTest\TestCase;

/**
 * Class ScalarDtoTest
 * Tests storing scalar values
 * @package DtoTest\ScalarObjects
 */
class HydrateScalarDtoTest extends TestCase
{
    public function testString()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'string');

        $dto->hydrate('My string');

        $this->assertEquals('My string', $dto->toScalar());
        $this->assertTrue(is_string($dto->toScalar()));
    }

    public function testInteger()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'integer');

        $dto->hydrate(123);

        $this->assertEquals(123, $dto->toScalar());
        $this->assertTrue(is_integer($dto->toScalar()));
    }

    public function testNumber()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'number');

        $dto->hydrate(123.45);

        $this->assertEquals(123.45, $dto->toScalar());
    }

    public function testBoolean()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'boolean');

        $dto->hydrate(false);

        $this->assertEquals(false, $dto->toScalar());
    }

    public function testNull()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'null');

        $dto->hydrate(null);

        $this->assertEquals(null, $dto->toScalar());
    }
}