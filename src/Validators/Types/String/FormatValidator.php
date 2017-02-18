<?php

namespace Dto\Validators\Types\String;

use Dto\Exceptions\InvalidFormatException;

/**
 * Class Formatter
 * See http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3
 * @package Dto
 */
class FormatValidator implements FormatValidatorInterface
{
    /**
     * Allowed formats mapped to internal function names
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3.7
     * @var array
     */
    protected $allowed_formats = [
        'date-time' => 'asDateTime',
        'email' => 'asEmail',
        'hostname' => 'asHostname',
        'ipv4' => 'asIpv4',
        'ipv6' => 'asIpv6',
        'uri' => 'asUri',
        'uriref' => 'asUriRef',
    ];

    /**
     * Disambiguate the name of the format
     *
     * @param $format
     * @param $value
     * @return mixed
     * @throws InvalidFormatException
     */
    public function check($format, $value)
    {
        if (!is_scalar($format) || !isset($this->allowed_formats[$format])) {
            throw new InvalidFormatException('Supported "format" values are: ' . implode(',',
                    array_keys($this->allowed_formats)));
        }

        return $this->{$this->allowed_formats[$format]}($value);
    }

    /**
     * Example: 2005-08-15T15:52:01+00:00
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3.1
     * @link https://tools.ietf.org/html/rfc3339 The official date-time spec
     * @param $value
     * @return mixed
     */
    public function asDateTime($value)
    {
        $datetime = new \DateTime($value);

        return ($value == $datetime->format(DATE_ATOM)) ? $value : false;
    }

    /**
     * @link https://tools.ietf.org/html/rfc5322#section-3.4.1
     * @link https://stackoverflow.com/questions/28280176/php-filter-var-and-rfc-5322
     *
     * @param $value
     * @return mixed filtered data on success, false on failure
     */
    public function asEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @link https://tools.ietf.org/html/rfc1034
     * @param $value
     * @return mixed
     */
    public function asHostname($value)
    {
        // There's not much in the PHP 5 core to support this... so I'm going to fudge it and reuse the email filter.
        if ($this->asEmail('test@'.$value) !== false) {
            return $value;
        }

        return false;
    }

    /**
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3.4
     * @link https://tools.ietf.org/html/rfc2673
     * @param $value
     * @return mixed
     */
    public function asIpv4($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3.5
     * @param $value
     * @return mixed
     */
    public function asIpv6($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * RFC3986
     * @link https://tools.ietf.org/html/rfc3986
     * @link https://gist.github.com/kpobococ/92f120c6c4a9a52b84e3
     * @param $value
     * @return mixed
     */
    public function asUri($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    /**
     * Similar to asUri, but allows for relative URIs
     * @link https://tools.ietf.org/html/rfc3986#section-4.2
     * @link https://d-mueller.de/blog/why-url-validation-with-filter_var-might-not-be-a-good-idea/
     * @link http://stackoverflow.com/questions/13903415/regular-expression-which-validates-a-relative-url
     * @param $value
     * @return mixed
     */
    public function asUriRef($value)
    {
        // We fudge this again by doubling down on the asUri validator
        if ($this->asUri('http://www.example.com/'.ltrim($value,'/')) !== false) {
            return $value;
        }

        return false;
    }
}