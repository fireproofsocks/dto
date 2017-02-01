<?php

namespace DtoTest\Usage;

use Dto\Dto;
use Dto\DtoInterface;
use DtoTest\TestCase;

class SimpleTest extends TestCase
{
    public function test()
    {
        $dto = new Dto();
        $this->assertInstanceOf(DtoInterface::class, $dto);

        //print_r($dto); exit;
        $dto->hydrate('bar');
        print_r($dto); exit;
        //$dto->foo = 'bar';
    }
}