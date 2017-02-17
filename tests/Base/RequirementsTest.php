<?php

namespace DtoTest\Base;

use DtoTest\TestCase;

class RequirementsTest extends TestCase
{
    protected $required_classes = [
        '\Webmozart\Json\JsonDecoder',
        '\DateTime'
    ];

    public function testClasses()
    {
        foreach ($this->required_classes as $c) {
            $this->assertTrue(class_exists($c));
        }
    }
}