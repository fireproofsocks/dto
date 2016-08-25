<?php
class SetTypeHashTest extends DtoTest\TestCase
{
    public function testNullableLocationReturnsNullWhenSetToNull()
    {
        $value = $this->callProtectedMethod(new SetTypeHashTestDto(), 'setTypeHash', [null, 'x']);
        $this->assertNull($value);
    }
    
    public function testNotNullableLocationReturnsInstanceOfDtoWhenSetToNull()
    {
        $dto = new SetTypeHashTestDto();
        $value = $this->callProtectedMethod($dto, 'setTypeHash', [null, 'y']);
        $this->assertInstanceOf(get_class($dto), $value);
    }
    
    public function testSettingWithHash()
    {
        $hash = ['a' => 'xray', 'b' => 'yak'];
        $value = $this->callProtectedMethod(new SetTypeHashTestDto(), 'setTypeHash', [$hash, 'x']);
        $this->assertEquals($hash, $value->toArray());
    }
    
    public function testSettingWithHashThruValueMutators()
    {
        $hash = ['a' => '12a', 'b' => '13.1'];
        $value = $this->callProtectedMethod(new SetTypeHashTestDto(), 'setTypeHash', [$hash, 'y']);
        $this->assertEquals(['a' => 12, 'b' => 13], $value->toArray());
    }
    
    public function testSettingWithDtoThruValueMutators()
    {
        $valueDto = new \Dto\Dto(['a' => '12a', 'b' => '13.1']);
        $value = $this->callProtectedMethod(new SetTypeHashTestDto(), 'setTypeHash', [$valueDto, 'y']);
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
//    public function __construct()
//    {
//        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
//    }
    
}