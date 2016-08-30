<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class GetFunctionNameTest extends TestCase
{
    public function testSimpleFunctionName()
    {
        $dto = new \Dto\Dto();
        $value = $this->callProtectedMethod($dto, 'getFunctionName', ['set', 'something']);
        $this->assertEquals('setSomething', $value);
    }
    
    public function testFunctionNameForDeepIndexWithDots()
    {
        $dto = new \Dto\Dto();
        $value = $this->callProtectedMethod($dto, 'getFunctionName', ['set', 'something.really.cool']);
        $this->assertEquals('setSomethingReallyCool', $value);
    }
    
    public function testFunctionNameForDeepIndexWithDotsWithCapitalLetters()
    {
        $dto = new \Dto\Dto();
        $value = $this->callProtectedMethod($dto, 'getFunctionName', ['set', 'SOMETHING.REALLY.COOL']);
        $this->assertEquals('setSomethingReallyCool', $value);
    }
    
    public function testReturnsFalseWhenInputInvalid()
    {
        $dto = new \Dto\Dto();

        $value = $this->callProtectedMethod($dto, 'getFunctionName', ['', 'SOMETHING.REALLY.COOL']);
        $this->assertFalse($value);
        
        $value = $this->callProtectedMethod($dto, 'getFunctionName', ['set', '']);
        $this->assertFalse($value);
    }
    
    public function testThatItIsOkToPassFalseToMethodExists()
    {
        $this->assertFalse(method_exists($this, false));
    }
}