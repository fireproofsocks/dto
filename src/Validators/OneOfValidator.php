<?php

namespace Dto\Validators;

use Dto\Exceptions\InvalidOneOfException;
use Dto\JsonSchemaAccessorInterface;
use Dto\RegulatorInterface;

class OneOfValidator extends AbstractValidator implements ValidatorInterface
{
    protected $regulator;

    public function validate($value, array $schema)
    {
        $this->schemaAccessor = $this->container->make(JsonSchemaAccessorInterface::class)->factory($schema);
        $this->regulator = $this->container->make(RegulatorInterface::class);

        $passed_cnt = 0;

        if ($oneOf = $this->schemaAccessor->getOneOf()) {

            foreach ($oneOf as $schema_candidate) {
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

                    $passed_cnt++;
                }
                catch (\Exception $e) {
                    continue;
                }
            }
        }

        if ($passed_cnt !== 1) {
            throw new InvalidOneOfException('"oneOf" validation failed. '.$passed_cnt.' schemas were considered valid.');
        }

        return $value;

    }

}