<?php

namespace Dto;

interface TypeConverterInterface
{
    public function toObject($value);

    public function toArray($value);

    public function toString($value);

    public function toInteger($value);

    public function toNumber($value);

    public function toBoolean($value);

    public function toNull($value);
}