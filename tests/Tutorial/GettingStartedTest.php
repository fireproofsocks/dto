<?php
namespace DtoTest\Tutorial;

use Dto\Dto;
use DtoTest\TestCase;

class GettingStartedTest extends TestCase
{
    public function testDtoStoresAssociativeArray()
    {
        $dto = new Dto([
            "first_name" => "Mahatma",
            "last_name" => "Ghandi",
            "birth_year" => 1869
        ]);

        $this->assertEquals([
            "first_name" => "Mahatma",
            "last_name" => "Ghandi",
            "birth_year" => 1869
        ], $dto->toArray());
    }

    public function testDtoStoresStdClass()
    {
        $obj = new \stdClass();
        $obj->first_name = 'Mahatma';
        $obj->last_name = 'Ghandi';
        $obj->birth_year = 1869;

        $dto = new Dto($obj);

        $this->assertEquals([
            "first_name" => "Mahatma",
            "last_name" => "Ghandi",
            "birth_year" => 1869
        ], $dto->toArray());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidObjectValueException
     */
    public function testExceptionThrownIfInputDataIsMissingRequiredFields()
    {
        $data = [
            "first_name" => "Mahatma",
            "last_name" => "Ghandi",
            //    "birth_year" => 1869
        ];

        $schema = [
            'type' => 'object',
            'properties' => [
                'first_name' => ['type' => 'string'],
                'last_name' => ['type' => 'string'],
                'birth_year' => ['type' => 'integer'],
            ],
            'additionalProperties' => false,
            'required' => ['first_name', 'last_name', 'birth_year']
        ];

        $dto = new Dto($data, $schema);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidKeyException
     */
    public function testAdditionalPropertiesAreNotAllowedWhenTheSchemaDisallowsThem()
    {
        $data = [
            "first_name" => "Mahatma",
            "last_name" => "Ghandi",
            "birth_year" => 1869
        ];

        $schema = [
            'type' => 'object',
            'properties' => [
                'first_name' => ['type' => 'string'],
                'last_name' => ['type' => 'string'],
                'birth_year' => ['type' => 'integer'],
            ],
            'additionalProperties' => false,
            'required' => ['first_name', 'last_name', 'birth_year']
        ];

        $dto = new Dto($data, $schema);

        $dto->twitter_screen_name = 'This throws an exception when additionalProperties is false';
    }

    public function testDefaultValuesCanFulfillTheRequiredFields()
    {
        $data = [
            'first_name' => 'New',
            'last_name' => 'Guy',
        ];

        $schema = [
            'type' => 'object',
            'properties' => [
                'first_name' => ['type' => 'string'],
                'last_name' => ['type' => 'string'],
                'birth_year' => ['type' => 'integer'],
            ],
            'additionalProperties' => false,
            'required' => ['first_name', 'last_name', 'birth_year'],
            'default' => [
                'birth_year' => 2017,
            ]
        ];


        $dto = new Dto($data, $schema);

        $this->assertEquals([
            'first_name' => 'New',
            'last_name' => 'Guy',
            'birth_year' => 2017
        ], $dto->toArray());
    }

    public function testTypeHinting()
    {
        $person = new Person([
            'first_name' => 'Nelson',
            'last_name' => 'Mandela',
            'birth_year' => 1918,
        ]);

        $this->assertEquals([
            'first_name' => 'Nelson',
            'last_name' => 'Mandela',
            'birth_year' => 1918,
        ], $person->toArray());
    }
}

class Person extends Dto
{
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'first_name' => ['type' => 'string'],
            'last_name' => ['type' => 'string'],
            'birth_year' => ['type' => 'integer'],
        ],
        'additionalProperties' => false,
        'required' => ['first_name', 'last_name', 'birth_year']
    ];
}