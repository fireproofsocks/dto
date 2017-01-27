<?php

namespace Dto;

interface JsonDecoderInterface
{
    public function decode($string);
}