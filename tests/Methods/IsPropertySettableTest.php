<?php

namespace DtoTest\Methods;

use DtoTest\TestCase;

class IsPropertySettableTest extends TestCase
{
    public function testExplicitlyDefinedPropertiesAreSettable()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'properties', [
            'my_string' => ['type' => 'string']
        ]);

        $this->assertTrue($this->callProtectedMethod($dto, 'isPropertySettable', ['my_string']));
        $this->assertFalse($this->callProtectedMethod($dto, 'isPropertySettable', ['not-defined']));

    }

    public function testUndefinedPropertiesAreSettableOnlyWhenAdditionalPropertiesIsTrue()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'additionalProperties', true);
        $this->assertTrue($this->callProtectedMethod($dto, 'isPropertySettable', ['not-explicitly-defined']));
    }
}