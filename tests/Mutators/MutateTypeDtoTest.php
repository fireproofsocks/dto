<?php

namespace DtoTest\DeclareTypes;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use DtoTest\TestCase;

class MutateTypeDtoTest extends TestCase
{
    public function testTypeCastsNullValuesToDto()
    {
        $value = $this->callProtectedMethod(
            new \Dto\Dto(
                [],
                ['x' => null],
                ['x' => ['type' => 'dto', 'class' => 'DtoTest\DeclareTypes\MutateTypeDtoChildDto', 'nullable' => false]]
            ),
            'mutateTypeDto',
            [null, 'x', ['type' => 'dto', 'class' => 'DtoTest\DeclareTypes\MutateTypeDtoChildDto', 'nullable' => false]]
        );
        $this->assertNotNull($value);
    }
    
    public function testNotNullableLocationDoesNotReturnNullWhenSetToNull()
    {
        $value = $this->callProtectedMethod(
            new \Dto\Dto(
                [],
                ['x' => null],
                ['x' => ['type' => 'dto', 'class' => 'DtoTest\DeclareTypes\MutateTypeDtoChildDto', 'nullable' => false]]
            ),
            'mutateTypeDto',
            [null, 'x', ['type' => 'dto', 'class' => 'DtoTest\DeclareTypes\MutateTypeDtoChildDto', 'nullable' => false]]
        );
        $this->assertNotNull($value);
        $this->assertEquals('dog', $value->y);
    }
    
    public function testSettingPropertyWithDtoInstanceWorks()
    {
        $parent = new \Dto\Dto(
            [],
            ['x' => null],
            ['x' => ['type' => 'dto', 'class' => 'DtoTest\DeclareTypes\MutateTypeDtoChildDto', 'nullable' => false]]
        );
        $child = new MutateTypeDtoChildDto();
        $child->y = 'my-value';
        
        $value = $this->callProtectedMethod($parent, 'mutateTypeDto', [$child, 'x', ['type' => 'dto', 'class' => 'DtoTest\DeclareTypes\MutateTypeDtoChildDto', 'nullable' => false]]);
        
        $this->assertNotNull($value);
        $this->assertEquals($value->toArray(), $child->toArray());
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionThrownWhenDtoTypeMetaDataOmitsType()
    {
        $parent = new \Dto\Dto([], ['x' => null], ['x' => ['type' => 'dto', 'nullable' => false]]);
        $child = new MutateTypeDtoChildDto();
        $child->y = 'my-value';
    
        $this->callProtectedMethod($parent, 'mutateTypeDto', [$child, 'x']);
    }
}

class MutateTypeDtoChildDto extends \Dto\Dto
{
    protected $template = [
        'y' => 'dog',
    ];
}