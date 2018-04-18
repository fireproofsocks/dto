<?php
namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class ReferenceTest extends TestCase
{
    public function testResolveReferencesInSubSchemas()
    {
        $dto = new Dto(null, [
            'type' => 'object',
            'properties' => [
                's' => ['$ref' => __DIR__ . '/data/string.json'],
                'i' => ['$ref' => __DIR__ . '/data/integer.json'],
            ]
        ]);

        $dto->i = '42asdf4';
        $this->assertEquals(42, $dto->i->toScalar());
    }
    
    public function testResolvePhpClassnamesAsReferencesWithRequiredValidation()
    {
        $myArray = new MyArray();
        $myItem = new MyItem(['address1' => '123 Main st.']);

        $myArray->append($myItem);

        $this->assertEquals([['address1' => '123 Main st.']], $myArray->toArray());

    }
}

class MyArray extends Dto
{
    protected $schema = [
        'type' => 'array',
        'items' => [
            '$ref' => MyItem::class
        ]
    ];
}

class MyItem extends Dto
{
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'address1' => [
                'type' => 'string',
            ]
        ],
        'required' => ['address1']
    ];
}