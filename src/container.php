<?php

use Pimple\Container;
use Dto\RegulatorInterface;
use Dto\JsonSchemaRegulator;
use Dto\JsonSchemaAcessorInterface;
use Dto\JsonSchemaAccessor;

$container = new Container();

$container[RegulatorInterface::class] = function ($c) {
    return new JsonSchemaRegulator($c);
};

$container[JsonSchemaAcessorInterface::class] = function ($c) {
    return new JsonSchemaAccessor();
};

//$container['TypeDetector'] = function ($c) {
//    new TypeDetector();
//};
//$container['TypeConverter'] = function ($c) {
//    new TypeConverter();
//};
//
//$this->stringValidator = new StringValidator($this);
//$this->numberValidator = new NumberValidator($this);
//$this->objectValidator = new ObjectValidator($this);
//$this->arrayValidator = new ArrayValidator($this);

return $container;