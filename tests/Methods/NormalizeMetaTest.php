<?php
class NormalizeMetaTest extends DtoTest\TestCase
{
    public function testThatDotsArePrepended()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('normalizeMeta');
        $method->setAccessible(true);
        $meta = [
            'firstname' => 'x',
            '.lastname' => 'y',
            'mother.firstname' => 'a',
        ];
        
        $normalized = [
            '.firstname' => 'x',
            '.lastname' => 'y',
            '.mother.firstname' => 'a',
        ];

        $this->assertEquals($normalized, $method->invokeArgs($dto, [$meta]));
    }
}