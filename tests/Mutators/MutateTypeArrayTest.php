<?php
class MutateTypeArrayTest extends DtoTest\TestCase
{
    public function testThatArrayKeysAreStripped()
    {
        $dto = new MutateTypeArrayTestDto();
        $array = [
            'a' => 'ape', 'b' => 'balloon'
        ];
        $value = $this->callProtectedMethod($dto, 'mutateTypeArray', [$array, 'x']);

        $this->assertEquals(['ape', 'balloon'], (array) $value);
        $this->assertEquals(1, $dto->tickle_me);
    }
    
    public function testThatScalarValuesAreTypedToArrays()
    {
        $dto = new MutateTypeArrayTestDto();
        $scalar = 'cat';
        $value = $this->callProtectedMethod($dto, 'mutateTypeArray', [$scalar, 'x']);
        $this->assertEquals(['cat'], (array) $value);
    }
}

class MutateTypeArrayTestDto extends \Dto\Dto
{
    public $tickle_me = 0; // for testing only
    
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
    
    // override this
    public function mutateTypeHash($value, $index)
    {
        $this->tickle_me = $this->tickle_me + 1;
        return $value;
    }
}