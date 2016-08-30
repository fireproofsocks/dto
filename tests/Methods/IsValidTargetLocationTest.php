<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class IsValidTargetLocationTest extends TestCase
{
    public function testThatDefinedIndexIsValidTarget()
    {
        $template = [
            'my_index' => null
        ];
        
        $result = $this->callProtectedMethod(new IsValidTargetLocationTestDto(), 'isValidTargetLocation', ['my_index', $template]);
        $this->assertTrue($result);
    }
    
    public function testThatUndefinedIndexIsNotValidTarget()
    {
        $template = [
            'my_index' => null
        ];
        
        $result = $this->callProtectedMethod(new IsValidTargetLocationTestDto(), 'isValidTargetLocation', ['not-defined-in-template', $template]);
        $this->assertFalse($result);
    }
    
    public function testUndefinedIndexesAreAllowedWhenTheNodeIsAmbiguous()
    {
        $dto = new IsValidTargetLocationTestDto();
        $template = [
            'my_index' => null
        ];
        $dto->setMeta(['ambiguous' => true]);
        
        $result = $this->callProtectedMethod($dto, 'isValidTargetLocation', ['not-defined-in-template', $template]);
        $this->assertTrue($result);
    }
    
    public function testEmptyTemplateDoesNotRestrictTargets()
    {
        $template = [];
        $result = $this->callProtectedMethod(new IsValidTargetLocationTestDto(), 'isValidTargetLocation', ['does-not-exist', $template]);
        $this->assertTrue($result);
    }
    
    public function testEvenEmptyStringIsConsideredValidOnUndefinedTemplates()
    {
        $template = [];
        
        $result = $this->callProtectedMethod(new IsValidTargetLocationTestDto(), 'isValidTargetLocation', ['', $template]);
        $this->assertTrue($result);
    }
    
    public function testThatNormalizedLocationsCanBeUsed()
    {
        $template = [
            'my_index' => null,
        ];
        
        $result = $this->callProtectedMethod(new IsValidTargetLocationTestDto(), 'isValidTargetLocation', ['.my_index', $template]);
        $this->assertTrue($result);
    }
}

class IsValidTargetLocationTestDto extends \Dto\Dto
{
    protected $meta = [
        'type' => 'unknown'
    ];
    
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
    
    protected function getMeta($index)
    {
        return $this->meta;
    }
    // For forcing the meta
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }
}