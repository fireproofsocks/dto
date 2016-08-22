<?php
class SetTypeArrayTest extends PHPUnit_Framework_Testcase
{
    protected function getMethod($dto)
    {
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('setTypeArray');
        $method->setAccessible(true);
        return $method;
    }
    
    public function testThatArrayKeysAreStripped()
    {
        $dto = new SetTypeArrayTestDto();
        $method = $this->getMethod($dto);
        $array = [
            'a' => 'ape', 'b' => 'balloon'
        ];
        $value = $method->invokeArgs($dto, [$array, 'x']);
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