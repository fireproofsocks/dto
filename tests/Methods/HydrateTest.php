<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class HydrateTest extends TestCase
{
    public function testHydrateScalar()
    {
        $d = new Dto();
        $d->hydrate('pineapple');
        $this->assertEquals('pineapple', $d->toScalar());
    }

    public function testHydrateArray()
    {
        $d = new Dto();
        $d->hydrate(['pine', 'pineapple', 'apple', 'pen']);
        $this->assertEquals(['pine', 'pineapple', 'apple', 'pen'], $d->toArray());
    }

    public function testHydrateObject()
    {
        $d = new Dto();
        $d->hydrate(['pine' => 'pineapple']);
        $this->assertEquals(['pine' => 'pineapple'], $d->toArray());
    }
}