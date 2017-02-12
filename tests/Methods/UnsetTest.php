<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class UnsetTest extends TestCase
{

    public function testUnsetOnObject()
    {
        $d = new Dto(['a' => 'apple', 'b' => 'boy']);
        $d->offsetUnset('a');
        $this->assertEquals(['b' => 'boy'], $d->toArray());
    }

    public function testUnsetOnArray()
    {
        $d = new Dto(['a', 'b'], ['type' => 'array']);
        $d->offsetUnset(1);
        $this->assertEquals(['a'], $d->toArray());
    }

    public function testUnsetOnArrayMustReindexArray()
    {
        $d = new Dto(['a', 'b'], ['type' => 'array']);
        $d->offsetUnset(0);
        $this->assertEquals(['b'], $d->toArray());
    }

    public function testUnsetOnNonExplicitArrayMakesItLookLikeAnObject()
    {
        $d = new Dto(['a', 'b']);
        $d->offsetUnset(0);
        // This is probably not the behavior that you want/expect!!! Solution: declare your type!
        $this->assertEquals([1 => 'b'], $d->toArray());
    }

    public function testForget()
    {
        $d = new Dto(['a' => 'apple', 'b' => 'boy']);
        $d->forget('a');
        $this->assertEquals(['b' => 'boy'], $d->toArray());
    }
}