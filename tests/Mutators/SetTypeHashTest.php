<?php
class SetTypeHashTest extends PHPUnit_Framework_Testcase
{
    protected function getMethod($dto)
    {
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('setTypeHash');
        $method->setAccessible(true);
        return $method;
    }
    
    public function testNullableLocationReturnsNullWhenSetToNull()
    {
        $dto = new SetTypeHashTestDto();
        $method = $this->getMethod($dto);
        
        $value = $method->invokeArgs($dto, [null, 'x']);
        $this->assertNull($value);
    }
    
    public function testNotNullableLocationReturnsInstanceOfDtoWhenSetToNull()
    {
        $dto = new SetTypeHashTestDto();
        $method = $this->getMethod($dto);
    
        $value = $method->invokeArgs($dto, [null, 'y']);
        $this->assertInstanceOf(get_class($dto), $value);
    }
    
    public function testSettingWithHash()
    {
        $dto = new SetTypeHashTestDto();
        $method = $this->getMethod($dto);
    
        $hash = ['a' => 'xray', 'b' => 'yak'];
        $value = $method->invokeArgs($dto, [$hash, 'x']);
        //print_r($value->toArray()); exit;
        $this->assertEquals($hash, $value->toArray());
    }
    
    public function testSettingWithHashThruValueMutators()
    {
        $dto = new SetTypeHashTestDto();
        $method = $this->getMethod($dto);
        
        $hash = ['a' => '12a', 'b' => '13.1'];
        $value = $method->invokeArgs($dto, [$hash, 'y']);
        //print_r($value); exit;
        $this->assertEquals(['a' => 12, 'b' => 13], $value->toArray());
    }
    
    public function testSettingWithDtoThruValueMutators()
    {
        $dto = new SetTypeHashTestDto();
        $method = $this->getMethod($dto);
        
        $valueDto = new \Dto\Dto(['a' => '12a', 'b' => '13.1']);
        
        $value = $method->invokeArgs($dto, [$valueDto, 'y']);
        //print_r($value); exit;
        $this->assertEquals(['a' => 12, 'b' => 13], $value->toArray());
    }
}

class SetTypeHashTestDto extends \Dto\Dto
{
    public $tickle_me = 0; // for testing only
    
    protected $meta = [
        '.x' => [
            'type' => 'hash',
            'nullable' => true
        ],
        '.y' => [
            'type' => 'hash',
            'values' => [
                'type' => 'integer'
            ],
            'nullable' => false
        ]
    ];
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
    
}