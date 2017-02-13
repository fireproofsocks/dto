<?php
namespace DtoTest\Validators;

use Dto\Validators\EnumValidator;
use DtoTest\TestCase;

class EnumValidatorTest extends TestCase
{
    protected function getInstance()
    {
        $container = include __DIR__ . '/../../src/container.php';
        return new EnumValidator($container);
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