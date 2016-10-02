<?php

namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class RootMetaTest extends TestCase
{
    public function testPropertiesCanBeSetOnAmbigousParents()
    {
        $dto = new RootMetaTestDto();
        $dto->y = true;
        $this->assertTrue($dto->y);
    }
    
    public function testDefinedFieldsUseTheirOwnTypeFiltering()
    {
        $dto = new RootMetaTestDto();
        $dto->x = 'A String Should Convert to an integer for an integer field';
        $this->assertEquals(1, $dto->x);
    }

    public function testNonDefinedFieldsUseTypeFilteringFromTheParent()
    {
        $dto = new RootMetaTestDto();
        $dto->y = 1;
        $this->assertTrue($dto->y);
    }
}

class RootMetaTestDto extends Dto
{
    protected $template = [
        'x' => 0
    ];
    
    protected $meta = [
        '.' => [
            'type' => 'hash',
            'ambiguous' => true,
            'values' => [
                'type' => 'boolean'
            ]
        ]
    ];
}
