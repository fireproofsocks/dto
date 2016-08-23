<?php
namespace DtoTest;

class TestCase extends \PHPUnit_Framework_Testcase
{
    /**
     * Call a private or protected function for testing purposes
     * @param $obj
     * @param $method
     * @param array $args
     * @return ReflectionMethod
     */
    protected function callProtectedMethod($obj, $method, array $args=[])
    {
        $reflection = new \ReflectionClass(get_class($obj));
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
    
        return $method->invokeArgs($obj, $args);
    }
}