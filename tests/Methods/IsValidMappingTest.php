<?php
class IsValidMappingTest extends DtoTest\TestCase
{
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testThatWritingArraysToScalarLocationsIsNotAllowed()
    {
        $dto = new IsValidMappingTestDto();
        $dto->setMeta(['type' => 'scalar']);
        $template = [
            'my_index' => null,
        ];
        
        $this->callProtectedMethod($dto, 'isValidMapping', [['some','array'], 'my_index', $template]);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testThatWritingScalarsToArrayLocationsIsNotAllowed()
    {
        $dto = new IsValidMappingTestDto();
        $dto->setMeta(['type' => 'array']);
        $template = [
            'my_index' => null,
        ];
        
        $this->callProtectedMethod($dto, 'isValidMapping', ['my-scalar', 'my_index', $template]);
    }
    
}

class IsValidMappingTestDto extends \Dto\Dto
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