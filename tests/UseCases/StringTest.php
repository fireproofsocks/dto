<?php
namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class StringTest extends TestCase
{
    public function testRegularString()
    {
        $dto = new Dto('hot rats');
        $this->assertEquals('hot rats', $dto->toScalar());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testMinLength()
    {
        $schema = [
            'type' => 'string',
            'minLength' => 3
        ];

        $dto = new Dto('hot rats', $schema);
    }
}