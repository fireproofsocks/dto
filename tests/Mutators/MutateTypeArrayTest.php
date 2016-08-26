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
//print var_dump($value); exit;
        $this->assertEquals(['ape', 'balloon'], (array) $value);
        $this->assertEquals(1, $dto->tickle_me);
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