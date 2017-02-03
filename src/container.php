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
use Dto\ValidatorSelectorInterface;
use Dto\ValidatorSelector;
use Dto\TypeDetectorInterface;
use Dto\TypeDetector;

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

$container[ValidatorSelectorInterface::class] = function ($c) {
    return new ValidatorSelector($c);
};

$container[\Dto\Validators\EnumValidator::class] = function ($c) {
    return new \Dto\Validators\EnumValidator($c);
};

$container[\Dto\Validators\AnyOfValidator::class] = function ($c) {
    return new \Dto\Validators\AnyOfValidator($c);
};

$container[\Dto\Validators\TypeValidator::class] = function ($c) {
    return new \Dto\Validators\TypeValidator($c);
};


$container[TypeDetectorInterface::class] = function ($c) {
    return new TypeDetector();
};

$container[\Dto\TypeConverterInterface::class] = function ($c) {
    return new \Dto\TypeConverter();
};

// Specific type-validators: {$typename} . 'Validator'
$container['objectValidator'] = function ($c) {
    return new \Dto\Validators\Types\ObjectValidator($c);
};
$container['arrayValidator'] = function ($c) {
    return new \Dto\Validators\Types\ArrayValidator($c);
};
$container['stringValidator'] = function ($c) {
    return new \Dto\Validators\Types\StringValidator($c);
};
$container['integerValidator'] = function ($c) {
    return new \Dto\Validators\Types\IntegerValidator($c);
};
$container['numberValidator'] = function ($c) {
    return new \Dto\Validators\Types\NumberValidator($c);
};
$container['nullValidator'] = function ($c) {
    return new \Dto\Validators\Types\NullValidator($c);
};



return $container;