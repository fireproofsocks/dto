<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class SetTest extends TestCase
{
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testExceptionThrownForScalarTypes()
    {
        $d = new Dto();
        $d->set('x', 'y');
    }
}