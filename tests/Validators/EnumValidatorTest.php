<?php
namespace DtoTest\Validators;

use Dto\ServiceContainer;
use Dto\Validators\EnumValidator;
use DtoTest\TestCase;

class EnumValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new EnumValidator(new ServiceContainer());
    }
    
    public function testListedValuesValidate()
    {
        $e = $this->getInstance();

        $result = $e->validate('abc', ['enum' => ['abc', 'def']]);

        $this->assertEquals('abc', $result);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidEnumException
     */
    public function testNonListedValuesDoNotValidate()
    {
        $e = $this->getInstance();

        $e->validate('xyz', ['enum' => ['abc', 'def']]);
    }
}