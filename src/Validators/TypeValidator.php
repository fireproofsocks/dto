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

    public function validate($value, array $schema)
    {
        $this->schemaAccessor = $this->container[JsonSchemaAccessorInterface::class]->load($schema);

        $this->ensureValidType();

        $type = $this->schemaAccessor->getType();

        if ($this->canPerformTypeCasting($value, $type)) {
            $value = $this->typeCast($value, $type);
        }

        $this->loadSpecificValidator($value, (array) $type, $schema);

        return true;
    }

    protected function ensureValidType()
    {
        $types = (array) $this->schemaAccessor->getType();

        foreach ($types as $t) {
            if (!in_array($t, $this->valid_types)) {
                if (!is_string($t)) {
                    throw new InvalidTypeException('"type" must be a string value and one of the following: '. implode(',', $this->valid_types));
                }
                throw new InvalidTypeException('"type" "'.$t.'" is not one of the allowed types: '. implode(',', $this->valid_types));
            }
        }
        return true;
    }

    /**
     * At present, the only reasonably safe condition when we can type-cast the input value is when only ONE type is defined.
     * If type is defined as an array, e.g. "type": ["string","null"] then we cannot reliably know the intention of an input
     * value such as an empty string.
     *
     * @param $value
     * @param $type
     * @return bool
     */
    protected function canPerformTypeCasting($value, $type)
    {
        return (is_array($type)) ? false : true;
    }

    /**
     * TODO: this feels smelly... filtering AND validating.  Make this behavior configurable?
     * @param $value mixed
     * @param $type string
     * @return mixed
     */
    protected function typeCast($value, $type)
    {
        $converter = $this->container[TypeConverterInterface::class];
        $this->value = $converter->{'to'.$type}($value);
        $this->isFiltered = true;

        return $this->value;
    }

    protected function loadSpecificValidator($value, array $types, array $schema)
    {
        // must match one of the types
        foreach ($types as $t) {

        }
    }

}