<?php
namespace Dto\Validators;

use Dto\Exceptions\InvalidTypeException;

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

    public function validate($value)
    {
        $types = (array) $this->schema->getType();
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

}