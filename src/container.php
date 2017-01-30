<?php

use Pimple\Container;
use Dto\RegulatorInterface;
use Dto\JsonSchemaRegulator;
use Dto\JsonSchemaAccessorInterface;
use Dto\JsonSchemaAccessor;
use Dto\JsonDecoderInterface;
use Dto\JsonDecoder;
use Dto\ResolverInterface;
use Dto\Resolver;

$container = new Container();

$container[RegulatorInterface::class] = function ($c) {
    return new JsonSchemaRegulator($c);
};

$container[JsonSchemaAccessorInterface::class] = function ($c) {
    return new JsonSchemaAccessor($c);
};

$container[JsonDecoderInterface::class] = function ($c) {
    $decoder = new \Webmozart\Json\JsonDecoder();
    $decoder->setObjectDecoding(1); // associative array
    return new JsonDecoder($decoder);
};

$container[ResolverInterface::class] = function ($c) {
    return new Resolver($c);
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