<?php

namespace Dto\Validators;

use Dto\Exceptions\InvalidNotException;
use Dto\JsonSchemaAccessorInterface;
use Dto\RegulatorInterface;

class NotValidator extends AbstractValidator implements ValidatorInterface
{
    protected $regulator;

    public function validate($value, array $schema)
    {
        $this->schemaAccessor = $this->container->make(JsonSchemaAccessorInterface::class)->factory($schema);
        $this->regulator = $this->container->make(RegulatorInterface::class);

        $passes_validation = false;

        if ($schema_candidate = $this->schemaAccessor->getNot()) {

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

                $passes_validation = true;
            }
            catch (\Exception $e) {
                $passes_validation = false;
            }
        }

        if ($passes_validation) {
            throw new InvalidNotException('Validation failed because "not" schema passed validation.');
        }

        return $value;

    }

}