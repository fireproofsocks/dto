<?php
class GetValueMutatorTest extends DtoTest\TestCase
{
    public function testDefaultValueReturned()
    {
        $value = $this->callProtectedMethod(new \Dto\Dto(), 'getValueMutator', ['']);
        $this->assertEquals('mutateTypeUnknown', $value);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidMutatorException
     */
    public function testExceptionThrownForUndefinedTypeMutator()
    {
        $meta = [
            '.x' => [
                'type' => 'does_not_exist'
            ],
            '.' => [
                'type' => 'hash',
                'values' => [
                    'type' => 'does_not_exist'
                ]
            ]
        ];
        $dto = new \Dto\Dto([],[],$meta);
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getValueMutator');
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
        $dto = new TestGetValueMutatorDto([],[],$meta);
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getValueMutator');
        $method->setAccessible(true);
        
        $value = $method->invokeArgs($dto, ['x']);
        $this->assertEquals('mutateX', $value);
    }
    
//    public function testTypeLevelMutatorReturned()
//    {
//        $meta = [
//            '.x' => [
//                'type' => 'boolean'
//            ]
//        ];
//        $dto = new \Dto\Dto([],[],$meta);
//        $reflection = new ReflectionClass(get_class($dto));
//        $method = $reflection->getMethod('getValueMutator');
//        $method->setAccessible(true);
//
//        $value = $method->invokeArgs($dto, ['x']);
//        $this->assertEquals('setTypeBoolean', $value);
//    }
    
    public function testValueLevelMutator()
    {
        $meta = [
            '.x' => [
                'type' => 'array',
                'values' => [
                    'type' => 'boolean'
                ]
            ],
            '.' => [
                'type' => 'hash',
                'values' => [
                    'type' => 'integer'
                ]
            ]
        ];
        $dto = new \Dto\Dto([],[],$meta);
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getValueMutator');
        $method->setAccessible(true);
    
        $value = $method->invokeArgs($dto, ['x']);
        $this->assertEquals('mutateTypeInteger', $value);
    }
}

class TestGetValueMutatorDto extends \Dto\Dto {
    
    function mutateX($value) {
        return $value;
    }
}