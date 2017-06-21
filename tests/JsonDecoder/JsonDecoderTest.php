<?php

namespace DtoTest\JsonDecoder;

use Dto\Exceptions\JsonDecodingException;
use Dto\JsonDecoder;
use Dto\JsonDecoderInterface;
use DtoTest\TestCase;

class JsonDecoderTest extends TestCase
{
    protected function getInstance()
    {
        return new JsonDecoder();
    }

    public function testInstantiation()
    {
        $j = $this->getInstance();
        $this->assertInstanceOf(JsonDecoderInterface::class, $j);
    }

    public function testDecodeString()
    {
        $j = $this->getInstance();
        $data = $j->decodeString('{"a":"apple","b":"boy"}');
        $this->assertEquals(['a'=>'apple','b'=>'boy'], $data);
    }

    /**
     * @expectedException \Dto\Exceptions\JsonDecodingException
     */
    public function testDecodeStringThrowsExceptionWhenStringIsInvalid()
    {
        $j = $this->getInstance();
        $data = $j->decodeString('{"this is not valid json');
    }

    public function testDecodeFile()
    {
        $j = $this->getInstance();
        $data = $j->decodeFile(__DIR__.'/data/sample.json');
        $this->assertEquals(['a'=>'apple','b'=>'boy'], $data);
    }

    /**
     * @expectedException \Dto\Exceptions\JsonDecodingException
     */
    public function testDecodeFileThrowsExceptionWhenNotFound()
    {
        $j = $this->getInstance();
        $j->decodeFile(__DIR__.'/data/does_not_exist.json');
    }

}