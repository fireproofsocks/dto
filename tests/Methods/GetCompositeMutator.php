<?php
class GetCompositeMutator extends DtoTest\TestCase
{
    public function testDefaultValueReturned()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getCompositeMutator');
        $method->setAccessible(true);
    
        $value = $method->invokeArgs($dto, ['']);
        $this->assertEquals('setTypeHash', $value);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidMutatorException
     */
    public function testExceptionThrownForUndefinedTypeMutator()
    {
        $meta = [
            '.x' => [
                'type' => 'does_not_exist'
            ]
        ];
        $dto = new \Dto\Dto([],[],$meta);
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getCompositeMutator');
        $method->setAccessible(true);
        
        $method->invokeArgs($dto, ['x']);
    }
    
    public function testFieldLevelMutatorReturnedWhenMethodExists()
    {
        $meta = [
            '.x' => [
                'type' => 'scalar'
            ]
        ];
        $dto = new TestGetCompositeMutatorDto([],[],$meta);
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getCompositeMutator');
        $method->setAccessible(true);
        
        $value = $method->invokeArgs($dto, ['x']);
        $this->assertEquals('setX', $value);
    }
    
    public function testTypeLevelMutatorReturned()
    {
        $meta = [
            '.x' => [
                'type' => 'boolean'
            ]
        ];
        $dto = new \Dto\Dto([],[],$meta);
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getCompositeMutator');
        $method->setAccessible(true);
    
        $value = $method->invokeArgs($dto, ['x']);
        $this->assertEquals('setTypeBoolean', $value);
    }
}

class TestGetCompositeMutatorDto extends \Dto\Dto {
    
    function setX($value) {
        return $value;
    }
}