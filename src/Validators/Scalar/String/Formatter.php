<?php

namespace Dto;

/**
 * Class Formatter
 * See http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3
 * @package Dto
 */
class Formatter implements FormatterInterface
{
    /**
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3.1
     * @link https://tools.ietf.org/html/rfc3339 The official date-time spec
     * @param $value
     */
    public function asDateTime($value)
    {

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

    public function asHostname($value)
    {

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
     * @link https://gist.github.com/kpobococ/92f120c6c4a9a52b84e3
     * @param $value
     */
    public function asUri($value)
    {

    }

    /**
     * Similar to asUri, but allows for relative URIs
     * @link https://tools.ietf.org/html/rfc3986#section-4.2
     * @param $value
     */
    public function asUriRef($value)
    {

    }
}