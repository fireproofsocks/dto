<?php
namespace DtoTest\Validators;

use Dto\ServiceContainer;
use Dto\Validators\TypeValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class TypeValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new TypeValidator(new ServiceContainer());
    }

    public function testInstantiation()
    {
        $v = $this->getInstance();
        $this->assertInstanceOf(ValidatorInterface::class, $v);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidTypeException
     */
    public function testEnsureValidDefinition()
    {
        $v = $this->getInstance();
        $v->validate('ignored', [
            'type' => 'bogus'
        ]);
    }

    public function testTypecastingIsPerformedForSingularTypes()
    {
        $v = $this->getInstance();
        $result = $v->validate('42', [
            'type' => 'integer'
        ]);
        $this->assertEquals(42, $result);
    }
}