<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class GetCompositeMutator extends TestCase
{
    public function testDefaultValueReturned()
    {
        $dto = new \Dto\Dto();
        $value = $this->callProtectedMethod($dto, 'getCompositeMutator', ['']);
        $this->assertEquals('mutateTypeHash', $value);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidMutatorException
     */
    public function testExceptionThrownForUndefinedTypeMutator()
    {
        $meta = [
            '.x' => [
                'type' => 'does_not_exist'
            ]
        ];
        $dto = new \Dto\Dto([],[],$meta);
        $this->callProtectedMethod($dto, 'getCompositeMutator', ['x']);
    }
    
    public function testFieldLevelMutatorReturnedWhenMethodExists()
    {
        $meta = [
            '.x' => [
                'type' => 'scalar',
                'mutator' => 'mutateMyX'
            ]
        ];
        $dto = new TestGetCompositeMutatorDto([],[],$meta);
        $value = $this->callProtectedMethod($dto, 'getCompositeMutator', ['x']);
        $this->assertEquals('mutateMyX', $value);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidMutatorException
     */
    public function testFieldLevelMutatorThrowsExceptionWhenMethodDoesNotExist()
    {
        $meta = [
            '.x' => [
                'type' => 'scalar',
                'mutator' => 'thisFunctionDoesNotExist'
            ]
        ];
        $dto = new TestGetCompositeMutatorDto([],[],$meta);
        $value = $this->callProtectedMethod($dto, 'getCompositeMutator', ['x']);
        $this->assertEquals('mutateMyX', $value);
    }
    
    public function testTypeLevelMutatorReturned()
    {
        $meta = [
            '.x' => [
                'type' => 'boolean'
            ]
        ];
        $dto = new \Dto\Dto([],[],$meta);
        $value = $this->callProtectedMethod($dto, 'getCompositeMutator', ['x']);
        $this->assertEquals('mutateTypeBoolean', $value);
    }

    public function testArrayValueMutatorReturned()
    {
        $meta = [
            '.' => [
                'type' => 'array',
                'values' => [
                    'type' => 'dto',
                    'class' => 'MyDtoClass'
                ]
            ]
        ];
        $dto = new \Dto\Dto([],[],$meta);
        $value = $this->callProtectedMethod($dto, 'getCompositeMutator', ['.0']);
        $this->assertEquals('mutateTypeDto', $value);
    }
}

class TestGetCompositeMutatorDto extends \Dto\Dto {
    
    protected function mutateMyX($value) {
        return $value;
    }
}