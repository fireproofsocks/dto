<?php

namespace Dto;

// TODO: throw our own Exceptions to really keep the JsonDecoder separate
use Dto\Exceptions\JsonDecodingException;

class JsonDecoder implements JsonDecoderInterface
{
    /**
     * @inheritdoc
     */
    public function decodeString($string)
    {

        $result = json_decode($string, true);

        $last_error_code = json_last_error();

        if ($last_error_code != JSON_ERROR_NONE) {
            throw new JsonDecodingException(json_last_error_msg(), $last_error_code);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function decodeFile($filepath)
    {
        // https://stackoverflow.com/questions/272361/how-can-i-handle-the-warning-of-file-get-contents-function-in-php
        set_error_handler(
            create_function(
                '$severity, $message, $file, $line',
                'throw new \Dto\Exceptions\JsonDecodingException($message, $severity);'
            )
        );

        $content = file_get_contents($filepath);

        restore_error_handler();

        return $this->decodeString($content);
    }

}