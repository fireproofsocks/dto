<?php

class GetMutatorTest extends \DtoTest\TestCase
{
    public function testScalarValueForScalarMetaUsesValueMutator()
    {
        $dto = new GetMutatorTestDto();
        $dto->setMeta(['type' => 'scalar']);
        
        $value = 'some-scalar';
        $index = 'something';
        
        $value = $this->callProtectedMethod($dto, 'getMutator', [$value, $index]);
        $this->assertEquals('getValueMutator', $value);
    }
    
    public function testScalarValueForHashMetaUsesValueMutator()
    {
        $dto = new GetMutatorTestDto();
        $dto->setMeta(['type' => 'hash']);
        
        $value = 'some-scalar';
        $index = 'something';
        
        $value = $this->callProtectedMethod($dto, 'getMutator', [$value, $index]);
        $this->assertEquals('getCompositeMutator', $value);
    }
    
    public function testHashValueForHashMetaUsesCompositMutator()
    {
        $dto = new GetMutatorTestDto();
        $dto->setMeta(['type' => 'hash']);
        
        $value = ['some' => 'hash'];
        $index = 'something';
        
        $value = $this->callProtectedMethod($dto, 'getMutator', [$value, $index]);
        $this->assertEquals('getCompositeMutator', $value);
    }
    
    public function testHashValueForScalarMetaUsesCompositMutator()
    {
        $dto = new GetMutatorTestDto();
        $dto->setMeta(['type' => 'integer']);
        
        $value = ['some' => 'hash'];
        $index = 'something';
        
        $value = $this->callProtectedMethod($dto, 'getMutator', [$value, $index]);
        $this->assertEquals('getValueMutator', $value);
    }
    
    public function testNullValuesForUnknownTypesUseValueMutator()
    {
        $dto = new GetMutatorTestDto();
        $dto->setMeta(['type' => 'unknown']);
    
        $value = null;
        $index = 'something';
    
        $value = $this->callProtectedMethod($dto, 'getMutator', [$value, $index]);
        $this->assertEquals('getValueMutator', $value);
    }
    
    public function testScalarValuesForUnknownTypesUseValueMutator()
    {
        $dto = new GetMutatorTestDto();
        $dto->setMeta(['type' => 'unknown']);
        
        $value = 'some-scalar';
        $index = 'something';
        
        $value = $this->callProtectedMethod($dto, 'getMutator', [$value, $index]);
        $this->assertEquals('getValueMutator', $value);
    }
    
    public function testArrayValuesForUnknownTypesUseValueMutator()
    {
        $dto = new GetMutatorTestDto();
        $dto->setMeta(['type' => 'unknown']);
        
        $value = ['some', 'scalar'];
        $index = 'something';
        
        $value = $this->callProtectedMethod($dto, 'getMutator', [$value, $index]);
        $this->assertEquals('getCompositeMutator', $value);
    }
}

class GetMutatorTestDto extends \Dto\Dto
{
    protected $meta = [];
    
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
    
    protected function getMeta($index)
    {
        return $this->meta;
    }
    
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }
    
    protected function getCompositeMutator($index) {
        return __FUNCTION__;
    }
    
    protected function getValueMutator($index) {
        return __FUNCTION__;
    }
}