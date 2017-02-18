<?php
namespace Dto;

use Dto\Validators\AllOfValidator;
use Dto\Validators\AnyOfValidator;
use Dto\Validators\EnumValidator;
use Dto\Validators\NotValidator;
use Dto\Validators\OneOfValidator;
use Dto\Validators\Types\ArrayValidator;
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
        $this->container[RegulatorInterface::class] = function ($c) {
            return new JsonSchemaRegulator($this);
        };

        $this->container[JsonSchemaAccessorInterface::class] = function ($c) {
            return new JsonSchemaAccessor();
        };

        $this->container[JsonDecoderInterface::class] = function ($c) {
            $decoder = new \Webmozart\Json\JsonDecoder();
            $decoder->setObjectDecoding(1); // associative array
            return new JsonDecoder($decoder);
        };

        $this->container[ReferenceResolverInterface::class] = function ($c) {
            return new ReferenceResolver($this);
        };

        $this->container[ValidatorSelectorInterface::class] = function ($c) {
            return new ValidatorSelector($this);
        };

        $this->container[EnumValidator::class] = function ($c) {
            return new EnumValidator($this);
        };

        $this->container[AnyOfValidator::class] = function ($c) {
            return new AnyOfValidator($this);
        };

        $this->container[OneOfValidator::class] = function ($c) {
            return new OneOfValidator($this);
        };

        $this->container[AllOfValidator::class] = function ($c) {
            return new AllOfValidator($this);
        };

        $this->container[NotValidator::class] = function ($c) {
            return new NotValidator($this);
        };

        $this->container[TypeValidator::class] = function ($c) {
            return new TypeValidator($this);
        };

        $this->container[TypeDetectorInterface::class] = function ($c) {
            return new TypeDetector();
        };

        $this->container[TypeConverterInterface::class] = function ($c) {
            return new TypeConverter();
        };

        $this->container[FormatValidatorInterface::class] = function ($c) {
            return new FormatValidator();
        };

        // Specific type-validators: {$typename} . 'Validator'
        $this->container['objectValidator'] = function ($c) {
            return new ObjectValidator($this);
        };
        $this->container['arrayValidator'] = function ($c) {
            return new ArrayValidator($this);
        };
        $this->container['stringValidator'] = function ($c) {
            return new StringValidator($this);
        };
        $this->container['integerValidator'] = function ($c) {
            return new IntegerValidator($this);
        };
        $this->container['numberValidator'] = function ($c) {
            return new NumberValidator($this);
        };
        $this->container['nullValidator'] = function ($c) {
            return new NullValidator($this);
        };

    }
}