<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class MutateTypeStringTest extends TestCase
{
    public function testMutateTypeStringIsAnAliasForMutateTypeScalar()
    {
        $value = $this->callProtectedMethod(new MutateTypeStringTestDto(), 'mutateTypeString', [123, 'ignoreme']);
        $this->assertEquals('mutateTypeScalar', $value);
    }

}

class MutateTypeStringTestDto extends \Dto\Dto {
    
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
    
    protected function mutateTypeScalar($value, $index)
    {
        return __FUNCTION__;
    }
}