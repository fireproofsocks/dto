<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class ExistsTest extends TestCase
{
    public function testKeyWithStringValueExists()
    {
        $d = new Dto(['p' => 'pineapple']);
        $this->assertTrue($d->exists('p'));
    }

    public function testKeyWithNullValueExists()
    {
        $d = new Dto(['p' => null]);
        $this->assertTrue($d->exists('p'));
    }

    public function testNonExistantKeyDoesNotExist()
    {
        $d = new Dto(['p' => null]);
        $this->assertFalse($d->exists('q'));
    }

    public function testIndexWithStringExists()
    {
        $d = new Dto(['p','q','r']);
        $this->assertTrue($d->exists(2));
    }

    public function testIndexBeyondTheSizeOfArrayDoesNotExist()
    {
        $d = new Dto(['p','q','r']);
        $this->assertFalse($d->exists(3));
    }
}