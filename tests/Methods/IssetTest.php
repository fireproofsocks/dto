<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class IssetTest extends TestCase
{
    public function testIssetOnObjectReturnsTrueForExtantKeys()
    {
        $d = new Dto(['a' => 'apple']);
        $this->assertTrue($d->__isset('a'));
    }

    public function testIssetOnObjectReturnsTrueForNonexistantKeys()
    {
        $d = new Dto(['a' => 'apple']);
        $this->assertFalse($d->__isset('b'));
    }

    public function testNativeIssetReturnsTrueForExtantKeys()
    {
        $d = new Dto(['a' => 'apple']);
        $this->assertTrue(isset($d->a));
    }

    public function testNativeIssetReturnsFalseForNonexistantKeys()
    {
        $d = new Dto(['a' => 'apple']);
        $this->assertFalse(isset($d->b));
    }

    public function testIssetOnArrayReturnsTrueForExtantIndex()
    {
        $d = new Dto(['apple']);
        $this->assertTrue($d->__isset(0));
    }

    public function testIssetOnArrayReturnsFalseForNonExtantIndex()
    {
        $d = new Dto(['apple']);
        $this->assertFalse($d->__isset(1));
    }
}