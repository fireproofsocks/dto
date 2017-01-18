<?php

namespace spec\Dto;

use Dto\TypeDetector;
use PhpSpec\ObjectBehavior;

class TypeDetectorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TypeDetector::class);
    }

    // -------------------------- getType ----------------------------
    function it_detects_an_object_as_an_object()
    {
        $this->getType(new \stdClass())->shouldReturn('object');
    }

    function it_detects_an_associative_array_as_an_object()
    {
        $this->getType(['a' => 'b'])->shouldReturn('object');
    }

    function it_detects_an_empty_array_as_an_object()
    {
        $this->getType([])->shouldReturn('object');
    }

    //-------------------------- isObject ----------------------------
    function it_says_an_object_is_an_object()
    {
        $this->isObject(new \stdClass())->shouldReturn(true);
    }

    function it_says_an_empty_array_is_an_object()
    {
        $this->isObject([])->shouldReturn(true);
    }

    function it_says_a_simple_array_is_not_an_object()
    {
        $this->isObject(['three', 'blind', 'mice'])->shouldReturn(false);
    }

    function it_says_an_associative_array_is_an_object()
    {
        $this->isObject(['a'=>'apple','b'=>'boy'])->shouldReturn(true);
    }

    function it_says_a_string_is_not_an_object()
    {
        $this->isObject('my string')->shouldReturn(false);
    }

    function it_says_an_integer_is_not_an_object()
    {
        $this->isObject(123)->shouldReturn(false);
    }

    function it_says_a_number_is_not_an_object()
    {
        $this->isObject(123.456)->shouldReturn(false);
    }

    function it_says_a_boolean_is_not_an_object()
    {
        $this->isObject(true)->shouldReturn(false);
    }

    function it_says_null_is_not_an_object()
    {
        $this->isObject(null)->shouldReturn(false);
    }

    //-------------------------- isArray ----------------------------
    function it_says_an_object_is_not_an_array()
    {
        $this->isArray(new \stdClass())->shouldReturn(false);
    }

    function it_says_an_empty_array_is_an_array()
    {
        $this->isArray([])->shouldReturn(true);
    }

    function it_says_a_non_empty_array_is_an_array()
    {
        $this->isArray(['a', 'b', 'c'])->shouldReturn(true);
    }

    function it_says_an_associative_array_is_not_a_true_array()
    {
        $this->isArray(['a' => 'associative array'])->shouldReturn(false);
    }

    function it_says_a_string_is_not_an_array()
    {
        $this->isArray('my string')->shouldReturn(false);
    }

    function it_says_an_integer_is_not_an_array()
    {
        $this->isArray(123)->shouldReturn(false);
    }

    function it_says_a_number_is_not_an_array()
    {
        $this->isArray(123.456)->shouldReturn(false);
    }

    function it_says_a_boolean_is_not_an_array()
    {
        $this->isArray(true)->shouldReturn(false);
    }

    function it_says_null_is_not_an_array()
    {
        $this->isArray(null)->shouldReturn(false);
    }

    //-------------------------- isString ----------------------------
    function it_says_an_object_is_not_a_string()
    {
        $this->isString(new \stdClass())->shouldReturn(false);
    }

    function it_says_an_array_is_not_a_string()
    {
        $this->isString([])->shouldReturn(false);
    }

    function it_says_a_string_is_not_a_string()
    {
        $this->isString('my string')->shouldReturn(true);
    }

    function it_says_an_integer_is_not_a_string()
    {
        $this->isString(123)->shouldReturn(false);
    }

    function it_says_a_number_is_not_a_string()
    {
        $this->isString(123.456)->shouldReturn(false);
    }

    function it_says_a_boolean_is_not_a_string()
    {
        $this->isString(true)->shouldReturn(false);
    }

    function it_says_null_is_not_a_string()
    {
        $this->isString(null)->shouldReturn(false);
    }

    //-------------------------- isInteger ----------------------------
    function it_says_an_object_is_not_an_integer()
    {
        $this->isInteger(new \stdClass())->shouldReturn(false);
    }

    function it_says_an_array_is_not_an_integer()
    {
        $this->isInteger([])->shouldReturn(false);
    }

    function it_says_a_string_is_not_an_integer()
    {
        $this->isInteger('my string')->shouldReturn(false);
    }

    function it_says_an_integer_is_an_integer()
    {
        $this->isInteger(123)->shouldReturn(true);
    }

    function it_says_a_number_is_not_an_integer()
    {
        $this->isInteger(123.456)->shouldReturn(false);
    }

    function it_says_a_boolean_is_not_an_integer()
    {
        $this->isInteger(true)->shouldReturn(false);
    }

    function it_says_null_is_not_an_integer()
    {
        $this->isInteger(null)->shouldReturn(false);
    }

    //-------------------------- isNumber ----------------------------
    function it_says_an_object_is_not_a_number()
    {
        $this->isNumber(new \stdClass())->shouldReturn(false);
    }

    function it_says_an_array_is_not_a_number()
    {
        $this->isNumber([])->shouldReturn(false);
    }

    function it_says_a_string_is_not_a_number()
    {
        $this->isNumber('my string')->shouldReturn(false);
    }

    function it_says_an_integer_is_a_number()
    {
        $this->isNumber(123)->shouldReturn(true);
    }

    function it_says_a_number_is_a_number()
    {
        $this->isNumber(123.456)->shouldReturn(true);
    }

    function it_says_a_boolean_is_not_a_number()
    {
        $this->isNumber(true)->shouldReturn(false);
    }

    function it_says_null_is_not_a_number()
    {
        $this->isNumber(null)->shouldReturn(false);
    }

    //-------------------------- isBoolean ----------------------------
    function it_says_an_object_is_not_a_boolean()
    {
        $this->isBoolean(new \stdClass())->shouldReturn(false);
    }

    function it_says_an_array_is_not_a_boolean()
    {
        $this->isBoolean([])->shouldReturn(false);
    }

    function it_says_a_string_is_not_a_boolean()
    {
        $this->isBoolean('my string')->shouldReturn(false);
    }

    function it_says_an_integer_is_not_a_boolean()
    {
        $this->isBoolean(123)->shouldReturn(false);
    }

    function it_says_a_number_is_not_a_boolean()
    {
        $this->isBoolean(123.456)->shouldReturn(false);
    }

    function it_says_a_boolean_is_a_boolean()
    {
        $this->isBoolean(false)->shouldReturn(true);
    }

    function it_says_null_is_not_a_boolean()
    {
        $this->isBoolean(null)->shouldReturn(false);
    }

    //-------------------------- isNull ----------------------------
    function it_says_an_object_is_not_a_null()
    {
        $this->isNull(new \stdClass())->shouldReturn(false);
    }

    function it_says_an_array_is_not_a_null()
    {
        $this->isNull([])->shouldReturn(false);
    }

    function it_says_a_string_is_not_a_null()
    {
        $this->isNull('my string')->shouldReturn(false);
    }

    function it_says_an_integer_is_not_a_null()
    {
        $this->isNull(123)->shouldReturn(false);
    }

    function it_says_a_number_is_not_a_null()
    {
        $this->isNull(123.456)->shouldReturn(false);
    }

    function it_says_a_boolean_is_not_a_null()
    {
        $this->isNull(false)->shouldReturn(false);
    }

    function it_says_null_is_a_null()
    {
        $this->isNull(null)->shouldReturn(true);
    }

}
