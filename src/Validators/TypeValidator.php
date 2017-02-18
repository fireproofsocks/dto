<?php
namespace Dto\Validators;

use Dto\Exceptions\InvalidTypeException;
use Dto\JsonSchemaAccessorInterface;
use Dto\TypeConverterInterface;

class TypeValidator extends AbstractValidator implements ValidatorInterface
{
    protected $valid_types = [
        'null',
        'boolean',
        'object',
        'array',
        'number',
        'string',
        'integer' // integer JSON numbers SHOULD NOT be encoded with a fractional part.
    ];

    public function validate($value, array $schema, $do_typecasting = true)
    {
        $this->schemaAccessor = $this->container->make(JsonSchemaAccessorInterface::class)->factory($schema);

        $this->ensureValidDefinition();

        $type = $this->schemaAccessor->getType();

        if ($do_typecasting && $this->canPerformTypeCasting($value, $type)) {
            $value = $this->typeCast($value, $type);
        }

        $this->tryAllDefinedTypes($value, (array) $type, $schema);

        return $value;
    }

    protected function ensureValidDefinition()
    {
        $types = (array) $this->schemaAccessor->getType();

        foreach ($types as $t) {
            if (!in_array($t, $this->valid_types)) {
                throw new InvalidTypeException('"type" "'.$t.'" is not one of the allowed types: '. implode(',', $this->valid_types));
            }
        }

        return true;
    }

    /**
     * At present, the only reasonably safe condition when we can type-cast the input value is when only ONE type is defined.
     * If type is defined as an array, e.g. "type": ["string","null"] then we cannot reliably know the intention of an input
     * value such as an empty string.
     * TODO: enhance for cases where there are multiple types including one scalar and one aggregate, e.g. string, array
     * @param $value
     * @param $type
     * @return bool
     */
    protected function canPerformTypeCasting($value, $type)
    {
        return (is_array($type)) ? false : true;
    }

    /**
     * @param $value mixed
     * @param $type string
     * @return mixed
     */
    protected function typeCast($value, $type)
    {
        return $this->container->make(TypeConverterInterface::class)->{'to'.$type}($value);
    }

    /**
     * Shove the value through the validators corresponding to the defined type(s).  If we exhaust all possible types,
     * then we (re)throw the last exception.
     *
     * @param $value
     * @param array $types
     * @param array $schema
     * @throws \Exception
     */
    protected function tryAllDefinedTypes($value, array $types, array $schema)
    {
        $passed_validation = false;
        foreach ($types as $t) {
            try {
                $this->container->make($t . 'Validator')->validate($value, $schema);
                $passed_validation = true;
                return;
            }
            catch (\Exception $e) {
                // ignore for now
            }
        }

        // If we didn't pass validation, we (re)throw the last Exception
        if (!$passed_validation) {
            throw $e;
        }
    }

}