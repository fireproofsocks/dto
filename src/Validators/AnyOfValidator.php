<?php

namespace Dto\Validators;

use Dto\Exceptions\InvalidAnyOfException;
use Dto\JsonSchemaAccessorInterface;
use Dto\RegulatorInterface;

class AnyOfValidator extends AbstractValidator implements ValidatorInterface
{
    protected $regulator;

    public function validate($value, array $schema)
    {
        $this->schemaAccessor = $this->container[JsonSchemaAccessorInterface::class]->load($schema);
        $this->regulator = $this->container[RegulatorInterface::class];

        if ($anyOf = $this->schemaAccessor->getAnyOf()) {

            foreach ($anyOf as $schema_candidate) {
                try {
                    $schema_candidate = $this->regulator->compileSchema($schema_candidate);

                    $value = $this->regulator->getDefault($value);

                    $value = $this->regulator->preFilter($value, $schema_candidate, false);

                    $storage_type = $this->regulator->chooseDataStorageType($value, $schema_candidate);

                    if ($storage_type === 'object') {
                        $this->regulator->filterObject($value, $schema_candidate);
                    } elseif ($storage_type === 'array') {
                        $this->regulator->filterArray($value, $schema_candidate);
                    } else {
                        // already done in preFiltering
                    }

                    return $value;
                }
                catch (\Exception $e) {
                    continue;
                }
            }

            throw new InvalidAnyOfException('No matching schema found for "anyOf"', 0, $e);
        }

    }

}