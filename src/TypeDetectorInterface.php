<?php

namespace Dto;

interface TypeDetectorInterface
{
    public function isObject($value);

    public function isArray($value);

    public function isString($value);

    public function isInteger($value);

    public function isNumber($value);

    public function isBoolean($value);

    public function isNull($value);
}