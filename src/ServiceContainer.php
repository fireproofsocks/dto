<?php
namespace Dto;

use Dto\Validators\AllOfValidator;
use Dto\Validators\AnyOfValidator;
use Dto\Validators\EnumValidator;
use Dto\Validators\NotValidator;
use Dto\Validators\OneOfValidator;
use Dto\Validators\Types\ArrayValidator;
use Dto\Validators\Types\BooleanValidator;
use Dto\Validators\Types\IntegerValidator;
use Dto\Validators\Types\NullValidator;
use Dto\Validators\Types\NumberValidator;
use Dto\Validators\Types\ObjectValidator;
use Dto\Validators\Types\String\FormatValidator;
use Dto\Validators\Types\String\FormatValidatorInterface;
use Dto\Validators\Types\StringValidator;
use Dto\Validators\TypeValidator;
use Pimple\Container;

class ServiceContainer implements ServiceContainerInterface
{
    protected $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->init();
    }

    public function make($service)
    {
        return $this->container[$service];
    }

    public function bind($service, \Closure $closure)
    {
        $this->container[$service] = $closure;
    }

    protected function init()
    {
        // --------------------- Primary Services -----------------------------------------------------
        $this->container[RegulatorInterface::class] = function () {
            return new JsonSchemaRegulator($this);
        };

        $this->container[JsonSchemaAccessorInterface::class] = function () {
            return new JsonSchemaAccessor();
        };

        $this->container[JsonDecoderInterface::class] = function () {
            return new JsonDecoder();
        };

        $this->container[ReferenceResolverInterface::class] = function () {
            return new ReferenceResolver($this);
        };

        $this->container[ValidatorSelectorInterface::class] = function () {
            return new ValidatorSelector($this);
        };

        // --------------------- Validators -----------------------------------------------------
        $this->container[EnumValidator::class] = function () {
            return new EnumValidator($this);
        };

        $this->container[AnyOfValidator::class] = function () {
            return new AnyOfValidator($this);
        };

        $this->container[OneOfValidator::class] = function () {
            return new OneOfValidator($this);
        };

        $this->container[AllOfValidator::class] = function () {
            return new AllOfValidator($this);
        };

        $this->container[NotValidator::class] = function () {
            return new NotValidator($this);
        };

        $this->container[TypeValidator::class] = function () {
            return new TypeValidator($this);
        };

        $this->container[TypeDetectorInterface::class] = function () {
            return new TypeDetector();
        };

        $this->container[TypeConverterInterface::class] = function () {
            return new TypeConverter();
        };

        $this->container[FormatValidatorInterface::class] = function () {
            return new FormatValidator();
        };

        // --------------------- Data-Type Validators -----------------------------------------------------
        // Specific type-validators: {$typename} . 'Validator'
        $this->container['objectValidator'] = function () {
            return new ObjectValidator($this);
        };
        $this->container['arrayValidator'] = function () {
            return new ArrayValidator($this);
        };
        $this->container['stringValidator'] = function () {
            return new StringValidator($this);
        };
        $this->container['booleanValidator'] = function () {
            return new BooleanValidator($this);
        };
        $this->container['integerValidator'] = function () {
            return new IntegerValidator($this);
        };
        $this->container['numberValidator'] = function () {
            return new NumberValidator($this);
        };
        $this->container['nullValidator'] = function () {
            return new NullValidator($this);
        };

    }
}