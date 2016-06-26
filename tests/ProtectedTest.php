<?php
class ProtectedTest extends PHPUnit_Framework_Testcase
{
    public function testGetNormalizedKey()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getNormalizedKey');
        $method->setAccessible(true);

        $this->assertEquals('.cat', $method->invokeArgs($dto, ['cat']));
        $this->assertEquals('.cat', $method->invokeArgs($dto, ['.cat']));
        $this->assertEquals('.cat', $method->invokeArgs($dto, ['.cat.']));
    }

    public function testIsValidMetaKey()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('isValidMetaKey');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($dto, ['']));
        $this->assertFalse($method->invokeArgs($dto, ['.']));
        $this->assertFalse($method->invokeArgs($dto, ['cat..dog']));
        $this->assertFalse($method->invokeArgs($dto, ['..catdog']));
        $this->assertTrue($method->invokeArgs($dto, ['mother.father.dog']));
    }

    public function testGetMetaSubset()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getMetaSubset');
        $method->setAccessible(true);

        // Assume a normalized meta key format w leading dots
        $meta = [
            '.firstname' => 'x',
            '.lastname' => 'y',
            '.mother.firstname' => 'a',
            '.mother.lastname' => 'b',
        ];
        $trimmed = [
            '.firstname' => 'a',
            '.lastname' => 'b',
        ];
        $this->assertEquals($trimmed, $method->invokeArgs($dto, ['mother', $meta]));

    }

    public function testNormalizeMeta()
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
    
    public function testAutoDetectTypes()
    {
        $template = [
            'integer' => 0,
            'float' => 0.00, // i.e. number
            'boolean' => false,
            'string' => '', // strval - true/false converts to 1/0, arrays to "Array"
            'array' => [],
        ];

        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('autoDetectTypes');
        $method->setAccessible(true);


        $meta = $method->invokeArgs($dto, [$template, []]);

        $this->assertEquals('integer', $meta['.integer']['type']);
        $this->assertEquals('float', $meta['.float']['type']);
        $this->assertEquals('boolean', $meta['.boolean']['type']);
        $this->assertEquals('scalar', $meta['.string']['type']);
        $this->assertEquals('array', $meta['.array']['type']);
    }

    /*public function testAutoDetectTypes()
    {
        $template = [
            'integer' => 0,
            'float' => 0.00, // i.e. number
            'boolean' => false,
            'string' => '', // strval - true/false converts to 1/0, arrays to "Array"
            'array' => [],
        ];

        $dto = new \Dto\Dto([], $template);
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('autoDetectTypes');
        $method->setAccessible(true);
        // $dto->meta routes through the ArrayObject storage, so we have to come in the secret way
        $property = $reflection->getProperty('meta');
        $property->setAccessible(true);

        $method->invokeArgs($dto, []);

        $meta = $property->getValue($dto);

        $this->assertEquals('integer', $meta['.integer']['type']);
        $this->assertEquals('float', $meta['.float']['type']);
        $this->assertEquals('boolean', $meta['.boolean']['type']);
        $this->assertEquals('scalar', $meta['.string']['type']);
        $this->assertEquals('array', $meta['.array']['type']);
    }*/

}