<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class MutateTypeHashTest extends TestCase
{
    public function testTypeCastsNullToObject()
    {
        $value = $this->callProtectedMethod(new MutateTypeHashTestDto(), 'mutateTypeHash', [null, 'x', []]);
        $this->assertNotNull($value);
    }
    
    public function testNotNullableLocationReturnsInstanceOfDtoWhenSetToNull()
    {
        $dto = new MutateTypeHashTestDto();
        $value = $this->callProtectedMethod($dto, 'mutateTypeHash', [null, 'y']);
        $this->assertInstanceOf(get_class($dto), $value);
    }
    
    public function testSettingWithHash()
    {
        $hash = ['a' => 'xray', 'b' => 'yak'];
        $value = $this->callProtectedMethod(new MutateTypeHashTestDto(), 'mutateTypeHash', [$hash, 'x']);
        $this->assertEquals($hash, $value->toArray());
    }
    
    public function testSettingWithHashThruValueMutators()
    {
        $hash = ['a' => '12a', 'b' => '13.1'];
        $value = $this->callProtectedMethod(new MutateTypeHashTestDto(), 'mutateTypeHash', [$hash, 'y']);
        $this->assertEquals(['a' => 12, 'b' => 13], $value->toArray());
    }
    
    public function testSettingWithDtoThruValueMutators()
    {
        $valueDto = new \Dto\Dto(['a' => '12a', 'b' => '13.1']);
        $value = $this->callProtectedMethod(new MutateTypeHashTestDto(), 'mutateTypeHash', [$valueDto, 'y']);
        $this->assertEquals(['a' => 12, 'b' => 13], $value->toArray());
    }
}

class MutateTypeHashTestDto extends \Dto\Dto
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