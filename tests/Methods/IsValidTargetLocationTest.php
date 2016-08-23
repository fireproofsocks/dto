<?php
class IsValidTargetLocationTest extends DtoTest\TestCase
{
    public function testThatDefinedIndexIsValidTargetForScalar()
    {
        $template = [
            'my_index' => null
        ];
        
        $result = $this->callProtectedMethod(new IsValidTargetLocationTestDto(), 'isValidTargetLocation', ['x', 'my_index', $template]);
        $this->assertTrue($result);
    }
    
    public function testThatUndefinedIndexIsNotValidTargetForScalar()
    {
        $template = [
            'my_index' => null
        ];
        
        $result = $this->callProtectedMethod(new IsValidTargetLocationTestDto(), 'isValidTargetLocation', ['x', 'does-not-exist', $template]);
        $this->assertFalse($result);
    }
    
    public function testThatEmptyTemplateDoesNotRestrictTargets()
    {
        $template = [];
        $result = $this->callProtectedMethod(new IsValidTargetLocationTestDto(), 'isValidTargetLocation', ['x', 'does-not-exist', $template]);
        $this->assertTrue($result);
    }
    
    public function testThatTheRootLocationIsValid()
    {
        $values = [
            'x' => 'y'
        ];
        $template = [
            'my_index' => null,
        ];
        
        $result = $this->callProtectedMethod(new IsValidTargetLocationTestDto(), 'isValidTargetLocation', [$values, '.', $template]);
        $this->assertTrue($result);
    }
    
    public function testThatNormalizedLocationsCanBeUsed()
    {
        $template = [
            'my_index' => null,
        ];
        
        $result = $this->callProtectedMethod(new IsValidTargetLocationTestDto(), 'isValidTargetLocation', ['x', '.my_index', $template]);
        $this->assertTrue($result);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testThatWritingArraysToScalarLocationsIsNotAllowed()
    {
        $dto = new IsValidTargetLocationTestDto();
        $dto->setMeta(['type' => 'scalar']);
        $template = [
            'my_index' => null,
        ];
    
        $this->callProtectedMethod($dto, 'isValidTargetLocation', [['some','array'], 'my_index', $template]);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testThatWritingScalarsToArrayLocationsIsNotAllowed()
    {
        $dto = new IsValidTargetLocationTestDto();
        $dto->setMeta(['type' => 'array']);
        $template = [
            'my_index' => null,
        ];
        
        $this->callProtectedMethod($dto, 'isValidTargetLocation', ['my-scalar', 'my_index', $template]);
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