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
        $this->schemaAccessor = $this->container->make(JsonSchemaAccessorInterface::class)->factory($schema);
        $this->regulator = $this->container->make(RegulatorInterface::class);

        if ($anyOf = $this->schemaAccessor->getAnyOf()) {

            foreach ($anyOf as $schema_candidate) {
                try {
                    $schema_candidate = $this->regulator->compileSchema($schema_candidate);

                    $value = $this->regulator->getDefault($value);

                    $value = $this->regulator->rootFilter($value, $schema_candidate, false);

                    $storage_type = $this->regulator->chooseDataStorageType($value, $schema_candidate);

                    if ($storage_type === 'object') {
                        // $value will be an associative array
                        $value = $this->regulator->filterObject($value, $schema_candidate);
                    } elseif ($storage_type === 'array') {
                        // $value will be an array
                        $value = $this->regulator->filterArray($value, $schema_candidate);
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