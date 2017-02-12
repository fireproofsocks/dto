<?php

namespace Dto;

interface DtoInterface
{
    public function hydrate($value);

    public function get($index);

    public function set($index, $value);

    public function forget($index);

    public function exists($index);

    public function getSchema();

    public function toObject();

    public function toArray();

    public function toJson();

    public function toScalar();

    public function isScalar();
}