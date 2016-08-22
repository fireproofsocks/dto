<?php
class GetMetaTest extends PHPUnit_Framework_Testcase
{
    public function testThatMetaIsEmptyForNonExistentIndex()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getMeta');
        $method->setAccessible(true);
        
        // TODO: throw exception?
        $value = $method->invokeArgs($dto, ['non-existent-index']);
        $this->assertEquals(['type'=>'unknown'], $value);
    }
}