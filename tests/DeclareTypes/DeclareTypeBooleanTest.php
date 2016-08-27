<?php

class DeclareTypeBooleanTest extends \DtoTest\TestCase
{
    public function test()
    {
        $D = new DeclareTypeBooleanDto();
        $D->prop = true;
        $this->assertEquals(true, $D->prop);
    
        $D->prop = '6x';
        $this->assertEquals(true, $D->prop);
    
        $D->set('prop', 0);
        $this->assertEquals(false, $D->prop);
    
        
        $D->set('prop', 'Not a boolean', true); // Bypass checks
        $this->assertEquals('Not a boolean', $D->prop);
    }
}

class DeclareTypeBooleanDto extends \Dto\Dto
{
    protected $template = [
        'prop' => null,
    ];
    
    protected $meta = [
        'prop' => [
            'type' => 'boolean'
        ],
    ];
}