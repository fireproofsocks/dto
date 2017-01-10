<?php

namespace DtoTest\SimpleObject;

use Dto\Dto;
use DtoTest\TestCase;

class SimpleObjectTest extends TestCase
{
    protected function getDto()
    {
        return new SimpleObjectDto();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Dto::class, $this->getDto());
    }
    
    public function testSetGet()
    {
        $dto = $this->getDto();
        $dto->my_string = 'Hello';

        $this->assertEquals('Hello', $dto->my_string);
        $this->assertEquals('Hello', $dto['my_string']);
    }

    public function testExplicitlyDefinedPropertiesAreSettable()
    {
        $dto = $this->getDto();
        $this->assertTrue($this->callProtectedMethod($dto, 'isPropertySettable', ['my_string']));
        $this->assertFalse($this->callProtectedMethod($dto, 'isPropertySettable', ['not-defined']));

    }

    public function testUndefinedPropertiesAreSettableOnlyWhenAdditionalPropertiesIsTrue()
    {
        $dto = $this->getDto();
        $this->setProtectedProperty($dto, 'additionalProperties', true);
        $this->assertTrue($this->callProtectedMethod($dto, 'isPropertySettable', ['not-explicitly-defined']));
    }
}