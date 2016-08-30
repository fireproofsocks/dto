<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class FamilyTreeTest extends TestCase
{
    public function testMappingParentsAndGrandparents()
    {
        $me = new PersonDto(['first_name'=>'Seth', 'last_name'=> 'Sloth']);
        $me->mother = new PersonDto(['first_name'=>'Beth', 'last_name'=> 'Sloth']);
        $me->father = new PersonDto(['first_name'=>'Threetoed', 'last_name'=> 'Sloth']);
        $me->mother->mother = new PersonDto(['first_name'=>'Silvia', 'last_name'=> 'Crawler']);
        $me->mother->father = new PersonDto(['first_name'=>'Earnest', 'last_name'=> 'Crawler']);
        $me->father->mother = new PersonDto(['first_name'=>'Nana', 'last_name'=> 'Sloth']);
        $me->father->father = new PersonDto(['first_name'=>'Ignatius', 'last_name'=> 'Sloth']);
        
        $this->assertEquals(array (
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
                        ),
                    'father' =>
                        array (
                            'first_name' => 'Earnest',
                            'last_name' => 'Crawler',
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
                        ),
                    'father' =>
                        array (
                            'first_name' => 'Ignatius',
                            'last_name' => 'Sloth',
                        ),
                ),
        ), $me->toArray());
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
            'class' => 'DtoTest\DeclareTypes\PersonDto',
            'nullable' => true,
        ],
        'father' => [
            'type' => 'dto',
            'class' => 'DtoTest\DeclareTypes\PersonDto',
            'nullable' => true,
        ]
    ];
}