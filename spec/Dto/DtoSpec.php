<?php

namespace spec\Dto;

use Dto\Dto;
use Dto\Exceptions\InvalidDataTypeException;
use Dto\JsonSchema;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DtoSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Dto::class);
    }

    // ------------- store scalar types -----------------------
    function it_can_store_a_string()
    {
        $this->beConstructedWith('hello');
        $this->toScalar()->shouldReturn('hello');
    }

    function it_can_store_an_integer()
    {
        $this->beConstructedWith(123);
        $this->toScalar()->shouldReturn(123);
    }

    function it_can_store_a_number()
    {
        $this->beConstructedWith(123.456);
        $this->toScalar()->shouldReturn(123.456);
    }

    function it_can_store_a_boolean()
    {
        $this->beConstructedWith(true);
        $this->toScalar()->shouldReturn(true);
    }

    function it_can_store_a_null()
    {
        $this->beConstructedWith(null);
        $this->toScalar()->shouldReturn(null);
    }

    function it_cannot_represent_scalars_as_arrays()
    {
        $this->beConstructedWith('my scalar');
        $this->shouldThrow(InvalidDataTypeException::class)->duringToArray();
    }

    function it_cannot_represent_scalars_as_objects()
    {
        $this->beConstructedWith('my scalar');
        $this->shouldThrow(InvalidDataTypeException::class)->duringToObject();
    }

    function it_can_represent_scalars_as_json()
    {
        $this->beConstructedWith('my scalar');
        $this->toJson()->shouldReturn('"my scalar"');
    }

    function it_implements_the__toString_method()
    {
        $this->beConstructedWith('my string');
        $this->__toString()->shouldReturn('my string');
    }

    // -------------------- store Scalars with restrictions ------------------
    function it_can_store_an_integer_with_explicit_schema()
    {
        $this->beConstructedWith(123, new JsonSchema(['type' => 'integer']));
        $this->toScalar()->shouldReturn(123);
    }

    function it_typecasts_values_to_the_declared_type()
    {
        $this->beConstructedWith(123, new JsonSchema(['type' => 'string']));
        $this->toScalar()->shouldReturn('123');
    }


    // ---------------- store arrays --------------------------------
    function it_can_store_an_array_of_strings()
    {
        $this->beConstructedWith(['one', 'two', 'three']);
        $this->toArray()->shouldBeLike(['one', 'two', 'three']);
    }



    function it_can_store_an_array_of_arrays()
    {
        $this->beConstructedWith([['a1','a2'],['b1','b2'],['c1','c2']]);
        $this->toArray()->shouldBeLike([['a1','a2'],['b1','b2'],['c1','c2']]);
    }


    // ---------------- store object --------------------------------
    function it_can_store_a_stdClass_object()
    {
        $obj = new \stdClass();
        $obj->a = 'apple';
        $obj->b = 'boy';
        $obj->c = 'cat';
        $this->beConstructedWith($obj);
        $this->toArray()->shouldReturn(['a' => 'apple', 'b'=>'boy', 'c'=>'cat']);
    }

    function it_can_store_a_stdClass_object_when_it_is_explicitly_typed()
    {
        $obj = new \stdClass();
        $obj->a = 'apple';
        $obj->b = 'boy';
        $obj->c = 'cat';
        $this->beConstructedWith($obj, new JsonSchema(['type'=> 'object']));
        $this->toArray()->shouldBeLike(['a' => 'apple', 'b'=>'boy', 'c'=>'cat']);
    }

    function it_can_store_a_stdClass_object_when_it_and_its_properties_are_explicitly_typed()
    {
        $obj = new \stdClass();
        $obj->a = 'apple';
        $obj->b = 'boy';
        $obj->c = 123;
        $this->beConstructedWith($obj, new JsonSchema(['type'=> 'object', 'additionalProperties' => ['type' => 'string']]));
        $this->toArray()->shouldBeLike(['a' => 'apple', 'b'=>'boy', 'c'=>'123']);
    }

    function it_can_store_an_associative_array()
    {
        $this->beConstructedWith(['a' => 'apple', 'b'=>'boy', 'c'=>'cat']);
        $this->toArray()->shouldReturn(['a' => 'apple', 'b'=>'boy', 'c'=>'cat']);
    }

    function it_can_store_an_associative_array_when_it_is_explicitly_typed_as_an_object()
    {
        $this->beConstructedWith(['a' => 'apple', 'b'=>'boy', 'c'=>'cat'], new JsonSchema(['type'=> 'object']));
        $this->toArray()->shouldBeLike(['a' => 'apple', 'b'=>'boy', 'c'=>'cat']);
    }

    function it_can_store_an_array_of_objects()
    {
        $o1 = new \stdClass();
        $o1->a = 'apple';
        $o2 = new \stdClass();
        $o2->a = 'apricot';
        $o3 = new \stdClass();
        $o3->a = 'amber';

        $this->beConstructedWith([$o1, $o2, $o3]);
        $this->toArray()->shouldBeLike([['a' => 'apple'],['a' => 'apricot'],['a' => 'amber']]);
    }

    function it_can_store_an_array_of_objects_when_explicitly_defined()
    {
        $o1 = new \stdClass();
        $o1->a = 'apple';
        $o2 = new \stdClass();
        $o2->a = 'apricot';
        $o3 = new \stdClass();
        $o3->a = 'amber';

        $this->beConstructedWith([$o1, $o2, $o3], new JsonSchema(['type' => 'array', 'items'=> ['type' => 'object']]));
        $this->toArray()->shouldBeLike([['a' => 'apple'],['a' => 'apricot'],['a' => 'amber']]);
    }

//    function it_can_be_represented_as_an_array()
//    {
//        $this->toArray()->shouldReturn([]);
//    }
//
//    function it_can_be_represented_as_json()
//    {
//        $this->toJson()->shouldReturn('[]');
//    }
//
//    function it_can_be_represented_as_an_object()
//    {
//        // For some reason, shouldReturn and others do not pass the test.
//        $this->toObject()->shouldBeLike(new \stdClass());
//    }

}
