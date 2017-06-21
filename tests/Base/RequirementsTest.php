<?php

namespace DtoTest\Base;

use DtoTest\TestCase;

class RequirementsTest extends TestCase
{
    protected $required_classes = [
        '\DateTime'
    ];

    protected $required_functions = [
        'json_last_error',
        'json_last_error_msg'
    ];

    public function testClasses()
    {
        foreach ($this->required_classes as $c) {
            $this->assertTrue(class_exists($c));
        }
    }

    public function testFunctions()
    {
        foreach ($this->required_functions as $f) {
            $this->assertTrue(function_exists($f));
        }
    }
}