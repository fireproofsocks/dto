<?php
class NormalizeMetaTest extends PHPUnit_Framework_Testcase
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
            '.' => ['type' => 'hash', 'values' => ['type' => 'unknown']]
        ];
        $this->assertEquals($normalized, $method->invokeArgs($dto, [$meta]));
    }
}