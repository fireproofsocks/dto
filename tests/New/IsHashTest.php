<?php
class IsHashTest extends PHPUnit_Framework_Testcase
{
    public function testThatScalarsAreNotHashes()
    {
        $D = new \Dto\Dto();
        $int = 1;
        $str = 'string';
        $bool = false;
        $float = 1.1;
        $array = [];
        
        $this->assertFalse($D->isHash($int));
        $this->assertFalse($D->isHash($str));
        $this->assertFalse($D->isHash($bool));
        $this->assertFalse($D->isHash($float));
        $this->assertFalse($D->isHash($array));
        
    }
    
    public function testThatEmptyArraysAreNotHashes()
    {
        $D = new \Dto\Dto();
        $array = [];
        $this->assertFalse($D->isHash($array));
    }
    
    public function testThatNonEmptyArraysAreNotHashes()
    {
        $D = new \Dto\Dto();
        $array = ['a','b','c'];
        $this->assertFalse($D->isHash($array));
    
        $array = [0=>'a',1=>'b',2=>'c'];
        $this->assertFalse($D->isHash($array));
    }
    
    public function testThatObjectsAreNotHashes()
    {
        $D = new \Dto\Dto();
        $obj = new stdClass();
        $this->assertFalse($D->isHash($obj));
    }
    
    
    public function testThatAssociativeArraysAreHashes()
    {
        $D = new \Dto\Dto();
        $array = [1=>'a',2=>'b',3=>'c'];
        $this->assertTrue($D->isHash($array));
        
        $array = ['a'=>'ape','b'=>'bug','c'=>'cat'];
        $this->assertTrue($D->isHash($array));
    }
}