<?php

namespace Dto;

/**
 * Interface JsonDecoderInterface
 * One job: decode JSON strings or files.  This is a wrapper for whatever Json Decoder Package we want to use.
 * @package Dto
 */
interface JsonDecoderInterface
{
    public function decodeString($string);

    public function decodeFile($filepath);
}