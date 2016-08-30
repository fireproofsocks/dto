<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class MutateTypeScalarTest extends TestCase
{
    public function testIntegersConvertToStrings()
    {
        $value = $this->callProtectedMethod(new MutateTypeScalarTestDto(), 'mutateTypeScalar', [123, 'ignoreme']);
        $this->assertEquals('123', $value);
    }
    
    public function testBooleanConvertToStrings()
    {
        $value = $this->callProtectedMethod(new MutateTypeScalarTestDto(), 'mutateTypeScalar', [true, 'ignoreme']);
        $this->assertEquals('1', $value);
    
        $value = $this->callProtectedMethod(new MutateTypeScalarTestDto(), 'mutateTypeScalar', [false, 'ignoreme']);
        $this->assertEquals('', $value);
    }
    
    public function testNullConvertsToEmptyString()
    {
        $value = $this->callProtectedMethod(new MutateTypeScalarTestDto(), 'mutateTypeScalar', [null, 'ignoreme']);
        $this->assertEquals('', $value);
    }
}

class MutateTypeScalarTestDto extends \Dto\Dto {
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
}