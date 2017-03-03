<?php
namespace DtoTest\UseCases;

use Dto\Dto;
use Dto\DtoInterface;
use DtoTest\TestCase;

class RecursiveTest extends TestCase
{
    public function testInstantiation()
    {
        $p = new Person();
        $this->assertInstanceOf(DtoInterface::class, $p);
    }

    public function testPersonWithAName()
    {
        $person = new Person();

        $person->name = 'Bob';

        $this->assertEquals('Bob', $person->name);
    }

    public function testPersonWithAMother2()
    {
        $person = new Person(['name' => 'Bob', 'mother' => ['name' => 'Lily']]);


        $this->assertEquals('Bob', $person->name);
        $this->assertEquals('Lily', $person->mother->name);
    }

    public function testPersonWithAMother()
    {
        $person = new Person(['name' => 'Bob']);
        $mother = new Person(['name' => 'Lily']);

        $person->mother = $mother;

        $this->assertEquals('Bob', $person->name);
        $this->assertEquals('Lily', $person->mother->name);
    }

    public function testPersonCreatedWithRelations()
    {
        $person = new Person(['name' => 'Bob', 'mother' => ['name' => 'Mary'], 'father' => ['name' => 'Frank']]);
        $this->assertEquals('Bob', $person->name);
        $this->assertEquals('Mary', $person->mother->name);
        $this->assertEquals('Frank', $person->father->name);
    }

    public function testCreatedWithDtos()
    {
        $person = new Person(['name' => 'Bob']);
        $person->mother = new Person(['name' => 'Lily']);
        $person->father = new Person(['name' => 'Scott']);

        $this->assertEquals($person->toArray(), ['name' => 'Bob', 'mother'=> ['name' => 'Lily'], 'father' => ['name' => 'Scott']]);
    }

    public function testCreatedAsObjects()
    {
        $person = new Person();
        $person->name = 'Bob';
        $person->mother = ['name' => 'Lily'];
        $person->father = ['name' => 'Scott'];

        $this->assertEquals($person->toArray(), ['name' => 'Bob', 'mother'=> ['name' => 'Lily'], 'father' => ['name' => 'Scott']]);
    }

    public function testCreatedInOneGo()
    {
        $person = new Person(['name' => 'Bob', 'mother'=> ['name' => 'Lily'], 'father' => ['name' => 'Scott']]);
        $this->assertEquals($person->toArray(), ['name' => 'Bob', 'mother'=> ['name' => 'Lily'], 'father' => ['name' => 'Scott']]);
    }
}

class Person extends Dto
{
    protected $schema = [
        'id' => 'person',
        '$ref' => '#/definitions/person',
        'definitions' => [
            'person' => [
                'type' => 'object',
                'properties' => [
                    'name' => ['type' => 'string'],
                    'mother' => [
                        '$ref' => '#/definitions/person'
                    ],
                    'father' => [
                        '$ref' => '#/definitions/person'
                    ],
                ]
            ]
        ]
    ];
}