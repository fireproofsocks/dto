<?php
namespace DtoTest\Tutorial;

use Dto\Dto;
use DtoTest\TestCase;

class NumberTest extends TestCase
{
    public function testNumber()
    {
        $schema = [
            'type' => 'number'
        ];

        $dto = new Dto(42.33, $schema);

        $this->assertEquals(42.33, $dto->toScalar());
        $this->assertTrue(is_float($dto->toScalar()));
    }

    public function testMultipleOf()
    {
        $schema = [
            'type' => 'number',
            'multipleOf' => 3
        ];

        $dto = new Dto(12, $schema);
        $this->assertEquals(12, $dto->toScalar());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testMultipleOfEnforced()
    {
        $schema = [
            'type' => 'number',
            'multipleOf' => 3
        ];

        $dto = new Dto(null, $schema);
        $dto->hydrate(12);
        $dto->hydrate(13); // BOOM
    }


    public function testMaximum()
    {
        $schema = [
            'type' => 'number',
            'maximum' => 100.01
        ];

        $dto = new Dto(null, $schema);
        $dto->hydrate(100.01);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testMaximumThrowsException()
    {
        $schema = [
            'type' => 'number',
            'maximum' => 100.01
        ];

        $dto = new Dto(null, $schema);
        $dto->hydrate(100.02);
    }

    public function testExclusiveMaximum()
    {
        $schema = [
            'type' => 'number',
            'maximum' => 100.00,
            'exclusiveMaximum' => true
        ];

        $dto = new Dto(null, $schema);
        $dto->hydrate(99.99);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testExclusiveMaximumThrowsException()
    {
        $schema = [
            'type' => 'number',
            'maximum' => 100.00,
            'exclusiveMaximum' => true
        ];

        $dto = new Dto(null, $schema);
        $dto->hydrate(100.00);
    }

    public function testMinimum()
    {
        $schema = [
            'type' => 'number',
            'minimum' => 18.3
        ];

        $dto = new Dto(18.3, $schema);
        $this->assertEquals(18.3, $dto->toScalar());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testMinimumThrowsException()
    {
        $schema = [
            'type' => 'number',
            'minimum' => 18.3
        ];

        $dto = new Dto(18.2, $schema);
    }


    public function testExclusiveMinimum()
    {
        $schema = [
            'type' => 'number',
            'minimum' => 0.00,
            'exclusiveMinimum' => true
        ];

        $dto = new Dto(0.0001, $schema);
        $this->assertEquals(0.0001, $dto->toScalar());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testExclusiveMinimumThrowsException()
    {
        $schema = [
            'type' => 'number',
            'minimum' => 0.00,
            'exclusiveMinimum' => true
        ];

        $dto = new Dto(null, $schema);
    }

}