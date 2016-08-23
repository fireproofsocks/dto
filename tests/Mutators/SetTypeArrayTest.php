<?php
class SetTypeArrayTest extends DtoTest\TestCase
{
    public function testThatArrayKeysAreStripped()
    {
        $dto = new SetTypeArrayTestDto();
        $array = [
            'a' => 'ape', 'b' => 'balloon'
        ];
        $value = $this->callProtectedMethod($dto, 'setTypeArray', [$array, 'x']);

        $this->assertEquals(['ape', 'balloon'], $value);
        $this->assertEquals(1, $dto->tickle_me);
    }
}

class SetTypeArrayTestDto extends \Dto\Dto
{
    public $tickle_me = 0; // for testing only
    
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
    
    // override this
    public function setTypeHash($value, $index)
    {
        $this->tickle_me = $this->tickle_me + 1;
        return $value;
    }
}