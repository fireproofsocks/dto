<?php

namespace DtoTest\UseCases;

use DtoTest\TestCase;

class CustomMutatorsTest extends TestCase
{
    public function testMutatorMethodNamesPropertyInheritTemplateLocation()
    {
        $dto = new CustomMutatorsDto();
        $dto->x = 'anything';
        $dto->other->x = 'anything';
        
        $this->assertEquals('mutateX', $dto->x);
        $this->assertEquals('mutateOtherX', $dto->other->x);
    }
    
}

class CustomMutatorsDto extends \Dto\Dto
{
    protected $template = [
        'x' => '',
        'other' => [
            'x' => '',
        ]
    ];
    
    protected $meta = [
        '.x' => [
            'mutator' => 'mutateX'
        ],
        '.other.x' => [
            'mutator' => 'mutateOtherX'
        ]
    ];
    
    protected function mutateX($value, $index) {
        return __FUNCTION__;
    }
    
    protected function mutateOtherX($value, $index) {
        return __FUNCTION__;
    }
}