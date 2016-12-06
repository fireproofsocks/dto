<?php

namespace DtoTest\DeclareTypes;

use Dto\Dto;
use DtoTest\TestCase;

class GetMetaTest extends TestCase
{
    public function testThatMetaIsEmptyForNonExistentIndex()
    {
        $dto = new \Dto\Dto();
        
        // TODO: throw exception?
        $value = $this->callProtectedMethod($dto, 'getMeta', ['non-existent-index']);
        $this->assertEquals(['type'=>'unknown'], $value);
    }

    public function testArrayIndexesReturnValueTypes()
    {
        $dto = new GetMetaTestDto();

        $value = $this->callProtectedMethod($dto, 'getMeta', ['.0']);

        $this->assertEquals([
            'type' => 'dto',
            'class' => 'ThisIsMyTestClass'
        ], $value);
    }
}

class GetMetaTestDto extends Dto
{
    protected $meta = [
        '.' => [
            'type' => 'array',
            'values' => [
                'type' => 'dto',
                'class' => 'ThisIsMyTestClass'
            ]
        ]
    ];
    public function __construct()
    {
        // override
    }
}