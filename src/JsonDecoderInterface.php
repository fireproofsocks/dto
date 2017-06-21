<?php

namespace Dto;

/**
 * Interface JsonDecoderInterface
 * One job: decode JSON strings or files.  This is a wrapper for whatever Json Decoder Package we want to use.
 * @package Dto
 */
interface JsonDecoderInterface
{
    /**
     * @param $string string
     * @return array
     */
    public function decodeString($string);

    /**
     * @param $filepath string
     * @return array
     */
    public function decodeFile($filepath);
}