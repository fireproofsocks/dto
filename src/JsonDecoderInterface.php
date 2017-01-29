<?php

namespace Dto;

/**
 * Interface JsonDecoderInterface
 * Wrapper for whatever Decoder Package we want to use.
 * @package Dto
 */
interface JsonDecoderInterface
{
    public function decodeString($string);

    public function decodeFile($filepath);
}