<?php
namespace DtoTest\Validators;

use Dto\Validators\TypeValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class TypeValidatorTest extends TestCase
{
    protected function getInstance()
    {
        $container = include __DIR__ . '/../../src/container.php';
        return new TypeValidator($container);
    }

    public function testInstantiation()
    {
        $v = $this->getInstance();
        $this->assertInstanceOf(ValidatorInterface::class, $v);
    }
}