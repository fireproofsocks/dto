<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class AutoDetectTypesTest extends TestCase
{
    public function testBasicAutoDetectionForStandardTypes()
    {
        $template = [
            'integer' => 0,
            'float' => 0.00, // i.e. number
            'boolean' => false,
            'string' => '', // strval - true/false converts to 1/0, arrays to "Array"
            'array' => [],
        ];
        
        $dto = new \Dto\Dto();
        
        $meta = $this->callProtectedMethod($dto, 'autoDetectTypes', [$template, []]);
        
        $this->assertEquals('integer', $meta['.integer']['type']);
        $this->assertEquals('float', $meta['.float']['type']);
        $this->assertEquals('boolean', $meta['.boolean']['type']);
        $this->assertEquals('scalar', $meta['.string']['type']);
        $this->assertEquals('array', $meta['.array']['type']);
        $this->assertEquals('unknown', $meta['.array']['values']['type']);
    }
    
    public function testValuesAssumedToBeNotNullable()
    {
        $template = [
            'integer' => 0,
            'float' => 0.00, // i.e. number
            'boolean' => false,
            'string' => '', // strval - true/false converts to 1/0, arrays to "Array"
            'array' => [],
        ];
        
        $dto = new \Dto\Dto();

        $meta = $this->callProtectedMethod($dto, 'autoDetectTypes', [$template, []]);
        
        
        $this->assertTrue(array_key_exists('nullable', $meta['.integer']));
        $this->assertTrue(array_key_exists('nullable', $meta['.float']));
        $this->assertTrue(array_key_exists('nullable', $meta['.boolean']));
        $this->assertTrue(array_key_exists('nullable', $meta['.string']));
        $this->assertTrue(array_key_exists('nullable', $meta['.array']));

        $this->assertFalse($meta['.integer']['nullable']);
        $this->assertFalse($meta['.float']['nullable']);
        $this->assertFalse($meta['.boolean']['nullable']);
        $this->assertFalse($meta['.string']['nullable']);
        $this->assertFalse($meta['.array']['nullable']);
    }
    
    public function testThatExplicitMetaDefinitionsAreNotOverwrittenByInferringTypesFromTemplate()
    {
        $template = [
            'x' => 'not-an-integer',
        ];
        $meta = [
            '.x' => [
                'type' => 'integer'
            ],
        ];
        
        $dto = new \Dto\Dto();
        
        $meta = $this->callProtectedMethod($dto, 'autoDetectTypes', [$template, $meta]);
        $this->assertFalse(isset($meta['x']));
        $this->assertTrue(isset($meta['.x']));
        $this->assertEquals('integer', $meta['.x']['type']);
    }
    
    public function testThatUnusedMetaValuesAreLeftAlone()
    {
        $template = [
            'x' => false,
        ];
        $meta = [
            'left-field' => ['type' => 'unknown']
        ];
        
        $dto = new \Dto\Dto();
        $meta = $this->callProtectedMethod($dto, 'autoDetectTypes', [$template, $meta]);
        $this->assertTrue(isset($meta['left-field']));
    }
    
    
}