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
        $this->assertTrue($method->invokeArgs($dto, ['.']));
        $this->assertFalse($method->invokeArgs($dto, ['cat..dog']));
        $this->assertFalse($method->invokeArgs($dto, ['..catdog']));
        $this->assertTrue($method->invokeArgs($dto, ['mother.father.dog']));
    }

    public function testGetMetaSubset1()
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

    /**
     * Meta Subset of an existing prefix should resolve to a "." (i.e. global) subset
     */
    public function testGetMetaSubset2()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getMetaSubset');
        $method->setAccessible(true);

        // Assume a normalized meta key format w leading dots
        $meta = [
            '.somehash' => [
                'type' => 'hash',
                'values' => 'boolean'
            ],
        ];

        $trimmed = [
            '.' => [
                'type' => 'hash',
                'values' => 'boolean'
            ],
        ];

        $this->assertEquals($trimmed, $method->invokeArgs($dto, ['somehash', $meta]));

    }

    /**
     * Meta subset of a non-existing prefix should come back empty
     */
    public function testGetMetaSubset3()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getMetaSubset');
        $method->setAccessible(true);

        // Assume a normalized meta key format w leading dots
        $meta = [
            '.somehash' => [
                'type' => 'hash',
                'values' => 'boolean'
            ],
        ];

        $trimmed = [];

        $this->assertEquals($trimmed, $method->invokeArgs($dto, ['non_existant', $meta]));

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
    
    public function testAutoDetectTypes1()
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
        $this->assertTrue(array_key_exists('nullable', $meta['.integer']));
        $this->assertFalse($meta['.integer']['nullable']);
        $this->assertEquals('float', $meta['.float']['type']);
        $this->assertFalse($meta['.float']['nullable']);
        $this->assertEquals('boolean', $meta['.boolean']['type']);
        $this->assertFalse($meta['.boolean']['nullable']);
        $this->assertEquals('scalar', $meta['.string']['type']);
        $this->assertFalse($meta['.string']['nullable']);
        $this->assertEquals('array', $meta['.array']['type']);
        $this->assertFalse($meta['.array']['nullable']);
    }

    public function testAutoDetectTypes2()
    {
        $template = [
            'x' => 0,
        ];
        $meta = [
            '.x' => [
                'type' => 'integer'
            ]
        ];

        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('autoDetectTypes');
        $method->setAccessible(true);


        $meta = $method->invokeArgs($dto, [$template, $meta]);


        $this->assertFalse(isset($meta['x']));
        $this->assertTrue(isset($meta['.x']));
    }

    public function testFilter()
    {
        $dto = new TestFilterDto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('filter');
        $method->setAccessible(true);

        // $dto->integer = '123a0'; // should come out as 123
        $value = $method->invokeArgs($dto, ['123a4', 'x']);
        $this->assertEquals(123, $value);

        $value = $method->invokeArgs($dto, ['456b7', 'y']);
        $this->assertEquals(456, $value);
    }

}

class TestFilterDto extends \Dto\Dto {
    protected $template = [
        'x' => null
    ];

    protected $meta = [
        'x' => [
            'type' => 'integer',
            'callback' => 'TestFilterDto::toInt',
        ],
        'y' => [
            'type' => 'integer'
        ]
    ];

    public static function toInt($value) {
        return intval($value);
    }

    public function setY($value) {
        return intval($value);
    }
}