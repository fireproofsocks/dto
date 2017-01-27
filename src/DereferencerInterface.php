<?php

namespace Dto;

interface DereferencerInterface
{
    public function resolveReference($string);
}