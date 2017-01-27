<?php

use Pimple\Container;
use Dto\RegulatorInterface;
use Dto\JsonSchemaRegulator;
use Dto\JsonSchemaAcessorInterface;
use Dto\JsonSchemaAccessor;
use Dto\JsonDecoderInterface;
use Dto\JsonDecoder;
use Dto\DereferencerInterface;
use Dto\Dereferencer;

$container = new Container();

$container[RegulatorInterface::class] = function ($c) {
    return new JsonSchemaRegulator($c);
};

$container[JsonSchemaAcessorInterface::class] = function ($c) {
    return new JsonSchemaAccessor($c);
};

$container[JsonDecoderInterface::class] = function ($c) {
    return new JsonDecoder();
};

$container[DereferencerInterface::class] = function ($c) {
    return new Dereferencer($c);
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