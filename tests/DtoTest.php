<?php

/**
 * Class DtoTest
 *
 * Test inclusion of other DTO files
 */
class DtoTest extends PHPUnit_Framework_Testcase
{
    public function testInstantiation()
    {
        $P = new TestParentDto();
        //print_r((array) $P); exit;
        //print_r($P->toArray()); exit;
        $this->assertEquals('default', $P->mydto->mystring);
        $this->assertEquals(42, $P->mydto->myinteger);
        $this->assertEquals(42.42, $P->mydto->myfloat);
    }
    
    public function testSetValues()
    {
        $P = new TestParentDto();
        $P->mydto->myinteger = 61;
        $this->assertEquals(61, $P->mydto->myinteger);
        $P->mydto->myinteger = '62a';
        $this->assertEquals(62, $P->mydto->myinteger);
    }
    
    public function testSetChild()
    {
        $P = new TestParentDto();
        $C = new TestChildDto();
        $C->mystring = 'custom';
        $P->mydto = $C;
        
        $this->assertEquals('custom', $P->mydto->mystring);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testSetDtoFail()
    {
        $P = new TestParentDto();
        $C = new TestParentDto();
        $P->mydto = $C;
    }
    
    public function testNullableIndexIsNull()
    {
        $P = new TestParentDto();
        $this->assertNotNull($P->mydto);
        $this->assertNull($P->mydto_nullable);
    }
}

class TestParentDto extends \Dto\Dto {
    protected $template = [
        'mydto' => null,
        'mydto_nullable' => null,
    ];
    protected $meta = [
        'mydto' => [
            'type' => 'dto',
            'class' => 'TestChildDto'
        ],
        'mydto_nullable' => [
            'type' => 'dto',
            'class' => 'TestChildDto',
            'nullable' => true
        ]
    ];
}

class TestChildDto extends \Dto\Dto {
    protected $template = [
        'mystring' => 'default',
        'myinteger' => 42,
        'myfloat' => 42.42
    ];
    
    protected $meta = [];
}