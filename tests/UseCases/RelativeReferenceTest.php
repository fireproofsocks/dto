<?php
namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class RelativeReferenceTest extends TestCase
{
    public function testBaseDir()
    {
        $dto = new Dto();
        $this->assertTrue(file_exists($dto->getBaseDir()));
        $this->assertTrue(is_dir($dto->getBaseDir()));
    }

    public function testOverriddenBaseDir()
    {
        $dto = new RelativeReferenceTestDto();
        $this->assertEquals(['type' => 'integer'], $dto->getSchema());
    }

    public function testSchemaUrl()
    {
        $file = 'file://' . __DIR__ . '/data/integer.json';
        $dto = new Dto(null, ['$ref' => $file]);
        $this->assertEquals(['type' => 'integer'], $dto->getSchema());
    }

    public function testNestedBaseDirectories()
    {
        $dto = new RelativeReferenceTest2Dto();

        $dto->i = 5;
        $this->assertEquals(5, $dto->i->toScalar());
    }
}

class RelativeReferenceTestDto extends Dto
{
    protected $baseDir = __DIR__;

    protected $schema = [
        '$ref' => 'data/integer.json'
    ];
}

class RelativeReferenceTest2Dto extends Dto
{
    protected $baseDir = __DIR__;

    protected $schema = [
        '$ref' => 'data/nested/object.json'
    ];
}