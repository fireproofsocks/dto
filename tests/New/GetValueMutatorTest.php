<?php
class GetValueMutatorTest extends PHPUnit_Framework_Testcase
{
    public function testDefaultValueReturned()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getValueMutator');
        $method->setAccessible(true);
        
        $value = $method->invokeArgs($dto, ['']);
        $this->assertEquals('setTypeUnknown', $value);
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
        $this->assertEquals('setX', $value);
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
        $this->assertEquals('setTypeInteger', $value);
    }
}

class TestGetValueMutatorDto extends \Dto\Dto {
    
    function setX($value) {
        return $value;
    }
}