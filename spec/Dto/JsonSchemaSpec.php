<?php

namespace spec\Dto;

use Dto\Exceptions\InvalidPropertyException;
use Dto\Exceptions\InvalidScalarValueException;
use Dto\JsonSchema;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JsonSchemaSpec extends ObjectBehavior
{
    protected function getDefaultSchema()
    {
        return include dirname(dirname(__DIR__)).'/src/default_root_schema.php';
    }
    function it_is_initializable()
    {
        $this->shouldHaveType(JsonSchema::class);
    }

    // --------------------- __construct() / getSchema() -------------------------
    function it_should_return_a_default_schema_for_getSchema()
    {
        $this->getPropertySchemaAsArray()->shouldReturn($this->getDefaultSchema());
    }

    function it_should_ignore_the_default_schema_what_provided_with_inputs()
    {
        $this->beConstructedWith(['type'=>'array']);
        $this->getPropertySchemaAsArray()->shouldReturn(['type'=>'array']);
    }

    function it_should_accept_string_filepaths_for_json_schema()
    {
        $this->beConstructedWith(__DIR__ . '/example-integer-spec.json');
        $this->getPropertySchemaAsArray()->shouldReturn(['type'=>'integer']);
    }

    function it_should_return_the_property_schema_for_explicitly_defined_properties()
    {
        $this->beConstructedWith(['properties' => ['foo'=> ['description' => 'yogurt']]]);
        $this->getPropertySchemaAsArray('foo')->shouldReturn(['description' => 'yogurt']);
    }

    function it_should_return_an_empty_schema_when_additionalProperties_is_true()
    {
        $this->beConstructedWith(['additionalProperties' => true]);
        $this->getPropertySchemaAsArray('foo')->shouldReturn([]);
    }

    function it_should_return_the_schema_when_additionalProperties_defines_a_schema_and_no_other_properties_are_matched()
    {
        $this->beConstructedWith(['additionalProperties' => ['description' => 'ostrich']]);
        $this->getPropertySchemaAsArray('foo')->shouldReturn(['description' => 'ostrich']);
    }

    function it_should_return_the_schema_matched_by_patternProperties_when_no_explicit_or_additionalProperties_are_defined()
    {
        $this->beConstructedWith(['patternProperties' => ['^S' => ['description' => 'starts with S']]]);
        $this->getPropertySchemaAsArray('Something')->shouldReturn(['description' => 'starts with S']);
    }
    function it_should_throw_exception_when_getting_schema_for_property_not_covered_explicity_by_properties_or_additionalProperties()
    {
        $this->beConstructedWith(['additionalProperties' => false]);
        $this->shouldThrow(InvalidPropertyException::class)->duringGetPropertySchemaAsArray('does-not-exist');
    }

    // ------------------------ getItemSchemaAsArray ---------------------
    function it_returns_the_item_schema_when_only_one_item_schema_exists()
    {
        $this->beConstructedWith(['items' => ['type' => 'string', 'description' => 'test']]);
        $this->getItemSchemaAsArray(0)->shouldReturn(['type' => 'string', 'description' => 'test']);
    }



    // -------------------- isObject ---------------------------
    function it_is_object_when_schema_type_is_object()
    {
        $this->beConstructedWith(['type' => 'object']);
        $this->isObject()->shouldReturn(true);
    }

    function it_is_object_when_schema_types_includes_object()
    {
        $this->beConstructedWith(['type' => ['null', 'object']]);
        $this->isObject()->shouldReturn(true);
    }

    // -------------------- isArray ---------------------------
    function it_is_array_when_schema_type_is_array()
    {
        $this->beConstructedWith(['type' => 'array']);
        $this->isArray()->shouldReturn(true);
    }

    function it_is_array_when_schema_types_includes_array()
    {
        $this->beConstructedWith(['type' => ['null', 'array']]);
        $this->isArray()->shouldReturn(true);
    }

    // -------------------- isScalar --------------------------
    function it_is_scalar_when_schema_type_is_string()
    {
        $this->beConstructedWith(['type' => 'string']);
        $this->isScalar()->shouldReturn(true);
    }

    function it_is_scalar_when_schema_types_includes_integer()
    {
        $this->beConstructedWith(['type' => ['integer']]);
        $this->isScalar()->shouldReturn(true);
    }

    function it_is_not_scalar_when_schema_type_is_object()
    {
        $this->beConstructedWith(['type' => 'object']);
        $this->isScalar()->shouldReturn(false);
    }

    // ------------------- getStorableTypeByValue --------------------------
    function it_is_storable_when_the_value_is_string_and_the_schema_type_is_string()
    {
        $this->beConstructedWith(['type' => 'string']);
        $this->getStorableTypeByValue('my string')->shouldReturn('string');
    }

    function it_is_storable_when_the_value_is_string_and_the_schema_type_includes_string()
    {
        $this->beConstructedWith(['type' => ['null', 'string']]);
        $this->getStorableTypeByValue('my string')->shouldReturn('string');
    }

    function it_is_not_storable_when_the_value_is_string_and_the_schema_type_omits_string()
    {
        $this->beConstructedWith(['type' => ['integer']]);
        $this->getStorableTypeByValue('my string')->shouldReturn(false);
    }

    // TODO: more for scalars... e.g. string to integer

    // ------------------ checkValidScalar ----------------------------


    // ------------------ getType ----------------------------
}
