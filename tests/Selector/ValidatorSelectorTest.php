<?php
namespace DtoTest\Selector;

use Dto\ServiceContainer;
use Dto\Validators\ValidatorInterface;
use Dto\ValidatorSelector;
use Dto\ValidatorSelectorInterface;
use DtoTest\TestCase;

class ValidatorSelectorTest extends TestCase
{

    protected function getInstance()
    {
        return new ValidatorSelector(new ServiceContainer());
    }

    public function testInstantation()
    {
        $s = $this->getInstance();
        $this->assertInstanceOf(ValidatorSelectorInterface::class, $s);
    }

    public function testEnum()
    {
        $s = $this->getInstance();

        $validators = $s->selectValidators([
            'enum' => ['a', 'b', 'c']
        ]);

        $this->assertEquals(1, count($validators));

        foreach ($validators as $v) {
            $this->assertInstanceOf(ValidatorInterface::class, $v);
        }
    }

    public function testAnyOf()
    {
        $s = $this->getInstance();

        $validators = $s->selectValidators([
            'anyOf' => [[], []]
        ]);

        $this->assertEquals(1, count($validators));

        foreach ($validators as $v) {
            $this->assertInstanceOf(ValidatorInterface::class, $v);
        }
    }


    public function testType()
    {
        $s = $this->getInstance();

        $validators = $s->selectValidators([
            'type' => ['string','null']
        ]);

        $this->assertEquals(1, count($validators));

        foreach ($validators as $v) {
            $this->assertInstanceOf(ValidatorInterface::class, $v);
        }
    }
}