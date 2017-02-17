<?php

namespace Dto\Validators\Types\String;

interface FormatValidatorInterface
{
    /**
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3.1
     * @link https://tools.ietf.org/html/rfc3339 The official date-time spec
     * @param $value
     */
    public function asDateTime($value);

    /**
     * @link https://tools.ietf.org/html/rfc5322#section-3.4.1
     * @link https://stackoverflow.com/questions/28280176/php-filter-var-and-rfc-5322
     *
     * @param $value
     * @return mixed filtered data on success, false on failure
     */
    public function asEmail($value);

    public function asHostname($value);

    /**
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3.4
     * @link https://tools.ietf.org/html/rfc2673
     * @param $value
     * @return mixed
     */
    public function asIpv4($value);

    /**
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3.5
     * @param $value
     * @return mixed
     */
    public function asIpv6($value);

    /**
     * @link https://gist.github.com/kpobococ/92f120c6c4a9a52b84e3
     * @param $value
     */
    public function asUri($value);

    /**
     * Similar to asUri, but allows for relative URIs
     * @link https://tools.ietf.org/html/rfc3986#section-4.2
     * @param $value
     */
    public function asUriRef($value);
}