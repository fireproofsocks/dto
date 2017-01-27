<?php

namespace Dto;

use Dto\Exceptions\JsonDecoderException;

class JsonDecoder implements JsonDecoderInterface
{
    public function decode($string)
    {
        $array = json_decode($string, true);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $array;
                break;
            case JSON_ERROR_DEPTH:
                throw new JsonDecoderException('Maximum stack depth exceeded');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new JsonDecoderException('Underflow or the modes mismatch');
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new JsonDecoderException('Unexpected control character found');
                break;
            case JSON_ERROR_SYNTAX:
                throw new JsonDecoderException('Syntax error, malformed JSON');
                break;
            case JSON_ERROR_UTF8:
                throw new JsonDecoderException('Malformed UTF-8 characters, possibly incorrectly encoded');
                break;
            default:
                throw new JsonDecoderException('Unknown error');
                break;
        }
    }

}