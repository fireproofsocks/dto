<?php

namespace DtoTest\ReferenceResolver;

use Dto\Dto;
use Dto\RegulatorInterface;
use Dto\ReferenceResolver;
use Dto\ReferenceResolverInterface;
use Dto\ServiceContainer;
use DtoTest\TestCase;

class ReferenceResolverTest extends TestCase
{

    protected function getInstance()
    {
        $container = new ServiceContainer();
        return new ReferenceResolver($container);
    }

    public function testInstantiation()
    {
        $d = $this->getInstance();
        $this->assertInstanceOf(ReferenceResolverInterface::class, $d);
    }

    public function testNullSchemaReturnsDefaultSchema()
    {
        $d = $this->getInstance();
        $this->assertEquals([], $d->resolveSchema(null));
    }

    public function testResolvingInlineSchema()
    {
        $d = $this->getInstance();
        $schema = $d->resolveSchema([
            '$ref' => '#/definitions/foo',
            'definitions' => [
                'foo' => [
                    'id' => 'foo',
                    'title' => 'Bar bar'
                ]
            ]
        ]);

        $this->assertEquals([
            'id' => 'foo',
            'title' => 'Bar bar'
        ], $schema);
    }

    public function testResolvingRemoteSchema()
    {
        $d = $this->getInstance();
        $file = __DIR__ . '/data/ref_b.json';
        $schema = $d->resolveSchema(['$ref' => $file]);
        $this->assertEquals(['description' => 'Schema B (final)', 'type' => 'object'], $schema);
    }

    public function testRecursivelyResolvingRemoteSchema()
    {
        $d = $this->getInstance();
        $file = __DIR__ . '/data/root.json';
        $schema = $d->resolveSchema(['$ref' => $file]);
        $this->assertEquals(['description' => 'Schema B (final)', 'type' => 'object'], $schema);
    }

    public function testUseDtoAsSchema()
    {
        $d = $this->getInstance();
        $regulator = \Mockery::mock(RegulatorInterface::class)
            ->shouldReceive('compileSchema')
            ->andReturn(['title' => 'Testy test'])
            ->shouldReceive('getDefault')
            ->andReturn(null)
            ->shouldReceive('rootFilter')
            ->andReturn(null)
            ->shouldReceive('isObject')
            ->andReturn(false)
            ->shouldReceive('isArray')
            ->andReturn(false)
            ->shouldReceive('chooseDataStorageType')
            ->andReturn('scalar')
            ->getMock();

        $schema = $d->resolveSchema(new Dto(null, ['title' => 'Testy test'], $regulator));

        $this->assertEquals(['title' => 'Testy test'], $schema);
    }

    public function testPhpClassnamesAsRef()
    {
        $d = $this->getInstance();

        $schema = $d->resolveSchema(['$ref' => '\\DtoTest\\ReferenceResolver\\data\\SampleDto']);

        $this->assertEquals(['title' => 'Testy test'], $schema);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidReferenceException
     */
    public function testPhpClassnamesAsRefMustImplementDtoInterface()
    {
        $d = $this->getInstance();

        $d->resolveSchema(['$ref' => '\\DtoTest\\ReferenceResolver\\data\\BadSchema']);
    }

    public function testJsonReferencesPhpClassname()
    {
        $d = $this->getInstance();
        $file = __DIR__ . '/data/php_ref.json';

        $schema = $d->resolveSchema(['$ref' => $file]);

        $this->assertEquals(['title' => 'Testy test'], $schema);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidSchemaException
     */
    public function testResolvingBadSchema()
    {
        $d = $this->getInstance();
        $d->resolveSchema('not an array!');
    }
}