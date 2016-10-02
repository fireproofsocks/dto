<?php

namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\DeclareTypes\GetMutatorTestDto;
use DtoTest\TestCase;

class GetTemplateSubsetTest extends TestCase
{
    public function testGetTemplateSubsetReturnsEmptyArrayWhenIndexDoesNotExist()
    {
        $dto = new Dto();
        $template = [
            'flags' => [
                'published' => true,
            ]
        ];
        $result = $this->callProtectedMethod($dto, 'getTemplateSubset', ['x', $template]);
        $this->assertEquals([], $result);
    }
    
    public function testGetTemplateSubsetReturnsSubsetWhenKeyDoesExist()
    {
        $dto = new Dto();
        $template = [
            'flags' => [
                'published' => true,
            ]
        ];
        $result = $this->callProtectedMethod($dto, 'getTemplateSubset', ['flags', $template]);
        $this->assertEquals(['published' => true], $result);
    }
    
    public function testGetTemplateSubsetReturnsSubsetWhenKeysIsNormalized()
    {
        $dto = new Dto();
        $template = [
            'flags' => [
                'published' => true,
            ]
        ];
        $result = $this->callProtectedMethod($dto, 'getTemplateSubset', ['.flags', $template]);
        $this->assertEquals(['published' => true], $result);
    }
}

class GetTemplateSubsetTestDto extends Dto
{
    protected $template = [
        'flags' => [
            'published' => true,
        ]
    ];
}