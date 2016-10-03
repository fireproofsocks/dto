<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class GetValueMutatorTest extends TestCase
{
    public function testDefaultValueReturned()
    {
        $value = $this->callProtectedMethod(new \Dto\Dto(), 'getValueMutator', ['']);
        $this->assertEquals('mutateTypeUnknown', $value);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidMutatorException
     */
    public function testExceptionThrownForUndefinedTypeMutator()
    {
        $meta = [
            '.x' => [
                'type' => 'does_not_exist'
            ],
            '.' => [
                'type' => 'hash',
                'values' => [
                    'type' => 'does_not_exist'
                ]
            ]
        ];
        $dto = new \Dto\Dto([],[],$meta);
        $this->callProtectedMethod($dto, 'getValueMutator', ['x']);
    }
    
    public function testFieldLevelMutatorReturnedWhenMethodExists()
    {
        $meta = [
            '.x' => [
                'type' => 'scalar',
                'mutator' => 'mutateMyX'
            ]
        ];
        $dto = new TestGetValueMutatorDto([],[],$meta);
        $value = $this->callProtectedMethod($dto, 'getValueMutator', ['x']);
        $this->assertEquals('mutateMyX', $value);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidMutatorException
     */
    public function testFieldLevelMutatorFailsWhenMethodDoesNotExist()
    {
        $meta = [
            '.x' => [
                'type' => 'scalar',
                'mutator' => 'does_not_exist'
            ]
        ];
        $dto = new TestGetValueMutatorDto([],[],$meta);
        $this->callProtectedMethod($dto, 'getValueMutator', ['x']);
    }
    
    
    public function testTypeLevelMutatorReturned()
    {
        $meta = [
            '.x' => [
                'type' => 'boolean'
            ]
        ];
        $dto = new \Dto\Dto([],[],$meta);
        $value = $this->callProtectedMethod($dto, 'getValueMutator', ['x']);
        $this->assertEquals('mutateTypeBoolean', $value);
    }
    
    public function testUseFieldTypeMutatorIfDefinedAndDoNotFallBackToParent()
    {
        $meta = [
            '.x' => [
                'type' => 'boolean',
            ],
            '.' => [
                'type' => 'hash',
                'values' => [
                    'type' => 'integer'
                ]
            ]
        ];
        $dto = new \Dto\Dto([],[],$meta);
        $value = $this->callProtectedMethod($dto, 'getValueMutator', ['x']);
        $this->assertEquals('mutateTypeBoolean', $value);
    }
    
    /**
     *
     */
    public function testDeferToParentCustomMutatorWhenAFieldHasNoMutatorDefined()
    {
        $meta = [
            '.x' => [
                // empty -- no data defined for this node
            ],
            '.' => [
                'type' => 'hash',
                'values' => [
                    'type' => 'integer',
                    'mutator' => 'mutateMyX'
                ]
            ]
        ];
        $dto = new TestGetValueMutatorDto([],[],$meta);
        $value = $this->callProtectedMethod($dto, 'getValueMutator', ['x']);
        $this->assertEquals('mutateMyX', $value);
    }
}

class TestGetValueMutatorDto extends \Dto\Dto {
    
    function mutateMyX($value) {
        return $value;
    }
}