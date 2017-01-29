<?php

namespace Dto;

// TODO: throw our own Exceptions to really keep the JsonDecoder separate
class JsonDecoder implements JsonDecoderInterface
{
    protected $decoder;

    public function __construct(\Webmozart\Json\JsonDecoder $decoder)
    {
        $this->decoder = $decoder;
    }

    public function decodeString($string)
    {
        return $this->decoder->decode($string);
    }

    public function decodeFile($filepath)
    {
        return $this->decoder->decodeFile($filepath);
    }

}