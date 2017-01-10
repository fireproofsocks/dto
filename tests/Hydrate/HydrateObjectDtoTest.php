<?php

namespace DtoTest\Hydrate;

use DtoTest\TestCase;

class HydrateObjectDtoTest extends TestCase
{
    public function testObjectWithStringProperty()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'object');
        $this->setProtectedProperty($dto, 'properties', [
            'my_val' => ['type' => 'string']
        ]);


        $obj = new \stdClass();
        $obj->my_val = 'Hello';

        $dto->hydrate($obj);

        $this->assertEquals('Hello', $dto->my_val);
        $this->assertEquals('Hello', $dto['my_val']);
        $this->assertEquals('Hello', $dto->get('my_val'));
    }

    public function testArrayWithStringProperty()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'object');
        $this->setProtectedProperty($dto, 'properties', [
            'my_val' => ['type' => 'string']
        ]);

        $arr = [
            'my_val' => 'Hello'
        ];

        $dto->hydrate($arr);

        $this->assertEquals('Hello', $dto->my_val);
        $this->assertEquals('Hello', $dto['my_val']);
        $this->assertEquals('Hello', $dto->get('my_val'));
    }

    public function testObjectWithIntegerProperty()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'object');
        $this->setProtectedProperty($dto, 'properties', [
            'my_val' => ['type' => 'integer']
        ]);


        $obj = new \stdClass();
        $obj->my_val = '123';

        $dto->hydrate($obj);

        $this->assertEquals(123, $dto->my_val);
        $this->assertEquals(123, $dto['my_val']);
        $this->assertEquals(123, $dto->get('my_val'));
    }
}