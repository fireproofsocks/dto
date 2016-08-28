<?php
class CustomMutatorsTest extends \DtoTest\TestCase
{
    public function testMutatorMethodNamesPropertyInheritTemplateLocation()
    {
        $this->markTestIncomplete('Not yet supported');
        
        $dto = new CustomMutatorsDto();
        $dto->x = 'anything';
        $dto->other->x = 'anything';
        
        // The child object @ "other" would need to be aware of the parent object...
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
    
    protected function mutateX($value, $index) {
        return __FUNCTION__;
    }
    
    protected function mutateOtherX($value, $index) {
        return __FUNCTION__;
    }
}