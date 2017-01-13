<?php

namespace spec\Dto;

use Dto\TypeConverter;
use PhpSpec\ObjectBehavior;

class TypeConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TypeConverter::class);
    }

    //--------------------- toObject -----------------------------
    function it_leaves_objects_untouched_when_converting_to_object()
    {
        $some_object = new \stdClass();
        $some_object->has = 'a property';

        $this->toObject($some_object)->shouldReturn($some_object);
    }

    function it_converts_arrays_to_objects_when_converting_to_object()
    {
        $array = [
            'a' => 'apple',
            'b' => 'boy'
        ];

        $expected = new \stdClass();
        $expected->a = 'apple';
        $expected->b = 'boy';

        // Remember: you can't use "shouldReturn" because the same instance is not returned.
        $this->toObject($array)->shouldBeLike($expected);
    }

    function it_converts_strings_to_empty_object_when_converting_to_object()
    {
        $this->toObject('some string')->shouldBeLike(new \stdClass());
    }

    function it_converts_integer_to_empty_object_when_converting_to_object()
    {
        $this->toObject(123)->shouldBeLike(new \stdClass());
    }

    function it_converts_number_to_empty_object_when_converting_to_object()
    {
        $this->toObject(123.456)->shouldBeLike(new \stdClass());
    }

    function it_converts_boolean_to_empty_object_when_converting_to_object()
    {
        $this->toObject(true)->shouldBeLike(new \stdClass());
    }

    function it_converts_null_to_empty_object_when_converting_to_object()
    {
        $this->toObject(null)->shouldBeLike(new \stdClass());
    }

    //------------------ toArray --------------------
    function it_strips_array_keys_when_converting_to_array()
    {
        $array = [
            'a' => 'apple',
            'b' => 'boy'
        ];

        $this->toArray($array)->shouldReturn(['apple', 'boy']);
    }

    function it_converts_objects_to_array_and_strips_keys_when_converting_to_array()
    {
        $obj = new \stdClass();
        $obj->a = 'apple';
        $obj->b = 'boy';

        // Remember: you can't use "shouldReturn" because the same instance is not returned.
        $this->toArray($obj)->shouldBeLike(['apple', 'boy']);
    }

    function it_converts_strings_to_empty_array_when_converting_to_array()
    {
        $this->toArray('some string')->shouldBeLike([]);
    }

    function it_converts_integer_to_empty_array_when_converting_to_array()
    {
        $this->toArray(123)->shouldBeLike([]);
    }

    function it_converts_number_to_empty_array_when_converting_to_array()
    {
        $this->toArray(123.456)->shouldBeLike([]);
    }

    function it_converts_boolean_to_empty_array_when_converting_to_array()
    {
        $this->toArray(true)->shouldBeLike([]);
    }

    function it_converts_null_to_empty_array_when_converting_to_array()
    {
        $this->toArray(null)->shouldBeLike([]);
    }

    //------------------ toString --------------------
    function it_converts_stdClass_objects_to_empty_string_when_converting_to_string()
    {
        $obj = new \stdClass();
        $obj->a = 'apple';
        $obj->b = 'boy';

        $this->toString($obj)->shouldReturn('');
    }

    function it_runs_an_objects_toString_method_when_converting_to_string()
    {
        $this->toString(new ExampleClassWithToStringMethod())->shouldReturn('some string');
    }

    function it_converts_arrays_to_empty_string_when_converting_to_string()
    {
        $this->toString([])->shouldReturn('');
    }

    function it_converts_integers_to_string_value_when_converting_to_string()
    {
        $this->toString(123)->shouldReturn('123');
    }

    function it_converts_numbers_to_empty_string_when_converting_to_string()
    {
        $this->toString(123.456)->shouldReturn('123.456');
    }

    function it_converts_boolean_false_to_empty_string_when_converting_to_string()
    {
        $this->toString(false)->shouldReturn('');
    }

    function it_converts_boolean_true_to_one_when_converting_to_string()
    {
        $this->toString(true)->shouldReturn('1');
    }

    function it_converts_null_to_empty_string_when_converting_to_string()
    {
        $this->toString(null)->shouldReturn('');
    }

    //------------------ toInteger --------------------
    function it_converts_stdClass_objects_to_zero_when_converting_to_integer()
    {
        $obj = new \stdClass();
        $obj->a = 'apple';
        $obj->b = 'boy';

        $this->toInteger($obj)->shouldReturn(0);
    }

    function it_returns_zero_even_for_objects_with_a_toString_method_when_converting_to_integer()
    {
        $this->toInteger(new ExampleClassWithToStringMethod())->shouldReturn(0);
    }

    function it_converts_arrays_to_zero_when_converting_to_integer()
    {
        $this->toInteger([])->shouldReturn(0);
    }

    function it_leaves_integers_untouched_when_converting_to_integer()
    {
        $this->toInteger(123)->shouldReturn(123);
    }

    function it_converts_numbers_integers_when_converting_to_integer()
    {
        $this->toInteger(123.456)->shouldReturn(123);
    }

    function it_converts_boolean_false_to_zero_when_converting_to_integer()
    {
        $this->toInteger(false)->shouldReturn(0);
    }

    function it_converts_boolean_true_to_one_when_converting_to_integer()
    {
        $this->toInteger(true)->shouldReturn(1);
    }

    function it_converts_null_to_zero_when_converting_to_integer()
    {
        $this->toInteger(null)->shouldReturn(0);
    }

    //------------------ toNumber --------------------
    function it_converts_stdClass_objects_to_zero_when_converting_to_number()
    {
        $obj = new \stdClass();
        $obj->a = 'apple';
        $obj->b = 'boy';

        $this->toNumber($obj)->shouldReturn(0.0);
    }

    function it_returns_zero_even_for_objects_with_a_toString_method_when_converting_to_number()
    {
        $this->toNumber(new ExampleClassWithToStringMethod())->shouldBeLike(0.0);
    }

    function it_converts_arrays_to_zero_when_converting_to_number()
    {
        $this->toNumber([])->shouldBeLike(0.0);
    }

    function it_leaves_floats_untouched_when_converting_to_number()
    {
        $this->toNumber(123.456)->shouldReturn(123.456);
    }

    function it_converts_integers_to_doubles_when_converting_to_number()
    {
        $this->toNumber(123)->shouldBeLike(123.0);
    }

    function it_converts_boolean_false_to_zero_when_converting_to_number()
    {
        $this->toNumber(false)->shouldBeLike(0.0);
    }

    function it_converts_boolean_true_to_one_when_converting_to_number()
    {
        $this->toNumber(true)->shouldBeLike(1.0);
    }

    function it_converts_null_to_zero_when_converting_to_number()
    {
        $this->toNumber(null)->shouldBeLike(0.0);
    }

    //------------------ toBoolean --------------------
    function it_considers_empty_objects_as_false_when_converting_to_boolean()
    {
        $this->toBoolean(new \stdClass())->shouldReturn(false);
    }

    function it_considers_non_empty_objects_as_true_when_converting_to_boolean()
    {
        $obj = new \stdClass();
        $obj->something = 'is not empty';
        $this->toBoolean($obj)->shouldReturn(true);
    }

    function it_considers_empty_arrays_as_false_when_converting_to_boolean()
    {
        $this->toBoolean([])->shouldReturn(false);
    }

    function it_considers_non_empty_arrays_as_true_when_converting_to_boolean()
    {
        $this->toBoolean(['not', 'empty'])->shouldReturn(true);
    }

    function it_considers_empty_strings_as_false_when_converting_to_boolean()
    {
        $this->toBoolean('some-string')->shouldReturn(true);
    }

    function it_considers_non_empty_strings_as_true_when_converting_to_boolean()
    {
        $this->toBoolean('some-string')->shouldReturn(true);
    }

    function it_considers_boolean_true_as_true_when_converting_to_boolean()
    {
        $this->toBoolean(true)->shouldReturn(true);
    }

    function it_considers_boolean_false_as_true_when_converting_to_boolean()
    {
        $this->toBoolean(false)->shouldReturn(false);
    }

    function it_considers_null_as_false_when_converting_to_boolean()
    {
        $this->toBoolean(false)->shouldReturn(false);
    }

    //------------------ toNull --------------------

    function it_considers_empty_objects_as_null_when_converting_to_null()
    {
        $this->toNull(new \stdClass())->shouldReturn(null);
    }

    function it_considers_non_empty_objects_as_null_when_converting_to_null()
    {
        $obj = new \stdClass();
        $obj->something = 'is not empty';
        $this->toNull($obj)->shouldReturn(null);
    }

    function it_considers_empty_arrays_as_null_when_converting_to_null()
    {
        $this->toNull([])->shouldReturn(null);
    }

    function it_considers_non_empty_arrays_as_null_when_converting_to_null()
    {
        $this->toNull(['not', 'empty'])->shouldReturn(null);
    }

    function it_considers_empty_strings_as_null_when_converting_to_null()
    {
        $this->toNull('')->shouldReturn(null);
    }

    function it_considers_non_empty_strings_as_null_when_converting_to_null()
    {
        $this->toNull('some-string')->shouldReturn(null);
    }

    function it_considers_boolean_true_as_null_when_converting_to_null()
    {
        $this->toNull(true)->shouldReturn(null);
    }

    function it_considers_boolean_false_as_null_when_converting_to_null()
    {
        $this->toNull(false)->shouldReturn(null);
    }

    function it_considers_null_as_null_when_converting_to_null()
    {
        $this->toNull(null)->shouldReturn(null);
    }
}

class ExampleClassWithToStringMethod
{
    public function __toString()
    {
        return 'some string';
    }
}

