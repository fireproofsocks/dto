<?php

namespace Dto;

// TODO: throw our own Exceptions to really keep the JsonDecoder separate
use Dto\Exceptions\JsonDecodingException;

class JsonDecoder implements JsonDecoderInterface
{
    protected $decoder;

    public function __construct(\Webmozart\Json\JsonDecoder $decoder)
    {
        $this->decoder = $decoder;
    }

    public function decodeString($string)
    {
        try {
            return $this->decoder->decode($string);
        }
        catch (\Exception $e) {
            throw new JsonDecodingException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function decodeFile($filepath)
    {
        try {
            return $this->decoder->decodeFile($filepath);
        }
        catch (\Exception $e) {
            throw new JsonDecodingException($e->getMessage(), $e->getCode(), $e);
        }
    }

}