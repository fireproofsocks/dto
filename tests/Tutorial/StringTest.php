<?php
namespace DtoTest\Tutorial;

use Dto\Dto;
use DtoTest\TestCase;

class StringTest extends TestCase
{
    public function testMaxLength()
    {
        $schema = [
            'type' => 'string',
            'maxLength' => 3
        ];

        $dto = new Dto(null, $schema);
        $dto->hydrate('abc');
        $this->assertEquals('abc', $dto->toScalar());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testMaxLengthThrowsExceptionWhenExceeded()
    {
        $schema = [
            'type' => 'string',
            'maxLength' => 3
        ];

        $dto = new Dto(null, $schema);
        $dto->hydrate('abcd');
    }

    public function testMinLength()
    {
        $schema = [
            'type' => 'string',
            'minLength' => 3
        ];

        $dto = new Dto('all good', $schema);
        $this->assertEquals('all good', $dto->toScalar());
    }

    public function testMinLengthValueFromDefault()
    {
        $schema = [
            'type' => 'string',
            'minLength' => 3,
            'default' => 'a plausible default'
        ];

        $dto = new Dto(null, $schema);
        $this->assertEquals('a plausible default', $dto->toScalar());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testMinLengthThrowsExceptionWhenNotFilled()
    {
        $schema = [
            'type' => 'string',
            'minLength' => 3
        ];

        $dto = new Dto('all good', $schema);
        $dto->hydrate('no');
    }

    public function testPattern()
    {
        $schema = [
            'type' => 'string',
            'pattern' => '^[0-9]{3}-[0-9]{3}-[0-9]{4}$'
        ];

        $dto = new Dto('888-555-1212', $schema);

        $this->assertEquals('888-555-1212', $dto->toScalar());
    }

    public function testFormat()
    {
        $schema = [
            'type' => 'string',
            'format' => 'email'
        ];

        $dto = new Dto('somebody@test.com', $schema);

        $this->assertEquals('somebody@test.com', $dto->toScalar());
    }
}