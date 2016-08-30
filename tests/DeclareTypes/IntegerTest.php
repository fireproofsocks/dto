<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class IntegerTest extends TestCase
{
    public function testWriteInteger()
    {
        $D = new DeclareTypeIntegerDto();
    
        $this->assertEquals(0, $D->prop); // template value should be filtered upon instantiation
        $this->assertNull($D->prop_nullable);
        
        $D->prop = 5;
        $this->assertEquals(5, $D->prop);
        
        $D->prop = '6x';
        $this->assertEquals(6, $D->prop);
        
        $D->set('prop', 7);
        $this->assertEquals(7, $D->prop);
        
        $D->set('prop', 'Not an Integer');
        $this->assertEquals(0, $D->prop);
        
        $D->set('prop', 'Not an Integer', true); // Bypass checks
        $this->assertEquals('Not an Integer', $D->prop);
    }
}
class DeclareTypeIntegerDto extends \Dto\Dto
{
    protected $template = [
        'prop' => null,
        'prop_nullable' => null,
    ];
    
    protected $meta = [
        'prop' => [
            'type' => 'integer'
        ],
    
        'prop_nullable' => [
            'type' => 'integer',
            'nullable' => true,
        ],
    ];
}