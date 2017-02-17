<?php
namespace DtoTest\Validators\Types;

use Dto\ServiceContainer;
use Dto\Validators\Types\StringValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class FormattedStringTest extends TestCase
{
    protected function getInstance()
    {
        return new StringValidator(new ServiceContainer());
    }

    public function testInstantiation()
    {
        $v = $this->getInstance();
        $this->assertInstanceOf(ValidatorInterface::class, $v);
    }

    public function testFormatPassesThruToFormatValidator()
    {
        $v = $this->getInstance();
        $result = $v->validate('abc@mail.com', [
            'format' => 'email'
        ]);
        $this->assertEquals($result, 'abc@mail.com');
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidFormatException
     */
    public function testExceptionThrownWhenFormatValidatorFails()
    {
        $v = $this->getInstance();
        $v->validate('not an email', [
            'format' => 'email'
        ]);
    }


    /**
     * @expectedException \Dto\Exceptions\InvalidFormatException
     */
    public function testExceptionThrownWhenFormatIsNotSupported()
    {
        $v = $this->getInstance();
        $v->validate('something', [
            'format' => 'not-valid'
        ]);
    }
}