<?php

namespace DtoTest;

use Dto\Dto;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function getDtoInstance()
    {
        return new Dto();
    }

    /**
     * Call a private or protected function for testing purposes
     * @param $obj
     * @param $method
     * @param array $args
     * @return mixed
     */
    protected function callProtectedMethod($obj, $method, array $args=[])
    {
        $reflection = new \ReflectionClass(get_class($obj));
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
    
        return $method->invokeArgs($obj, $args);
    }

    /**
     * @param $obj
     * @param $propertyName
     * @param $newVal
     */
    protected function setProtectedProperty($obj, $propertyName, $newVal)
    {
        $reflection = new \ReflectionClass(get_class($obj));

        $reflectionProperty = $reflection->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($obj, $newVal);
    }

    public function test()
    {
        // For some reason, this test class sometimes is executed independently.
        // Without any tests in it, PHPUnit issues a warning.
        $this->assertTrue(true);
    }
}