<?php
namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class EnumTest extends TestCase
{
    public function testSelection()
    {
        $selection = new Selection();

        $selection->hydrate('A');

        $this->assertEquals('A', $selection->toScalar());
    }
}

class Selection extends Dto
{
    protected $schema = [
        'enum' => [null, 'A', 'B', 'C', 'D']
    ];
}