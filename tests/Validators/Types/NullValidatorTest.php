<?php
namespace DtoTest\Validators\Types;

use Dto\Validators\Types\NullValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class NullValidatorTest extends TestCase
{
    protected function getInstance()
    {
        $container = include __DIR__ . '/../../../src/container.php';
        return new NullValidator($container);
    }

    public function testInstantiation()
    {
        $v = $this->getInstance();
        $this->assertInstanceOf(ValidatorInterface::class, $v);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testCheckDataTypeThrowsExceptionOnString()
    {
        $v = $this->getInstance();
        $v->validate('not null', []);
    }

    public function testNullIsNull()
    {
        $v = $this->getInstance();
        $actual = $v->validate(null, []);
        $this->assertNull($actual);
    }
}