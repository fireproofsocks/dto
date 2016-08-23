<?php
class GetMetaTest extends DtoTest\TestCase
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