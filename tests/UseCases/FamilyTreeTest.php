<?php

namespace DtoTest\UseCases;

use DtoTest\TestCase;

class FamilyTreeTest extends TestCase
{
    public $tree = array (
        'first_name' => 'Seth',
        'last_name' => 'Sloth',
        'mother' =>
            array (
                'first_name' => 'Beth',
                'last_name' => 'Sloth',
                'mother' =>
                    array (
                        'first_name' => 'Silvia',
                        'last_name' => 'Crawler',
                        'mother' => null,
                        'father' => null,
                    ),
                'father' =>
                    array (
                        'first_name' => 'Earnest',
                        'last_name' => 'Crawler',
                        'mother' => null,
                        'father' => null,
                    ),
            ),
        'father' =>
            array (
                'first_name' => 'Threetoed',
                'last_name' => 'Sloth',
                'mother' =>
                    array (
                        'first_name' => 'Nana',
                        'last_name' => 'Sloth',
                        'mother' => null,
                        'father' => null,
                    ),
                'father' =>
                    array (
                        'first_name' => 'Ignatius',
                        'last_name' => 'Sloth',
                        'mother' => null,
                        'father' => null,
                    ),
            ),
    );

    public function testMappingParentsAndGrandparents()
    {
        $me = new PersonDto(['first_name'=>'Seth', 'last_name'=> 'Sloth']);
        $me->mother = new PersonDto(['first_name'=>'Beth', 'last_name'=> 'Sloth']);
        $me->father = new PersonDto(['first_name'=>'Threetoed', 'last_name'=> 'Sloth']);
        $me->mother->mother = new PersonDto(['first_name'=>'Silvia', 'last_name'=> 'Crawler']);
        $me->mother->father = new PersonDto(['first_name'=>'Earnest', 'last_name'=> 'Crawler']);
        $me->father->mother = new PersonDto(['first_name'=>'Nana', 'last_name'=> 'Sloth']);
        $me->father->father = new PersonDto(['first_name'=>'Ignatius', 'last_name'=> 'Sloth']);
        
        $this->assertEquals($this->tree, $me->toArray());
    }

    public function testMappingHierarchicalDataFromConstructor()
    {
        $me = new PersonDto($this->tree);
        $this->assertEquals($this->tree, $me->toArray());
    }

    public function testInjectingDeeplyNestedDataWithExtraData()
    {
        $tree = $this->tree;
        $tree['cousin'] = [
            'first_name' => 'Delete',
            'last_name' => 'Me'
        ];
        $tree['mother']['buttons'] = 6;

        $me = new PersonDto($tree);
        $this->assertEquals($this->tree, $me->toArray());

        // Test using set
        $me = new PersonDto();
        $me->set('.', $tree);
        $this->assertEquals($this->tree, $me->toArray());
    }
}

class PersonDto extends \Dto\Dto
{
    protected $template = [
        'first_name' => '',
        'last_name' => '',
        'mother' => null,
        'father' => null
    ];
    
    protected $meta = [
        'mother' => [
            'type' => 'dto',
            'class' => 'DtoTest\UseCases\PersonDto',
            'nullable' => true,
        ],
        'father' => [
            'type' => 'dto',
            'class' => 'DtoTest\UseCases\PersonDto',
            'nullable' => true,
        ]
    ];
}