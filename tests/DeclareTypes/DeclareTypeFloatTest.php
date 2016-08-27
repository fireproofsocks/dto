<?php
class DeclareTypeFloatTest extends \DtoTest\TestCase
{
    public function test()
    {
        $D = new DeclareTypeFloatDto();
    
        $this->assertEquals(0, $D->prop);
        
        $D->prop = 5;
        $this->assertEquals(5, $D->prop);
    
        $D->prop = '6x';
        $this->assertEquals(6, $D->prop);
    
        $D->set('prop', 7.1);
        $this->assertEquals(7.1, $D->prop);
    
        $D->set('prop', 7.1000);
        $this->assertEquals(7.1, $D->prop);
        
        $D->set('prop', 'Not a Float');
        $this->assertEquals(0, $D->prop);
    
        $D->set('prop', 'Not a float', true); // Bypass checks
        $this->assertEquals('Not a float', $D->prop);
    }
}


class DeclareTypeFloatDto extends \Dto\Dto
{
    protected $template = [
        'prop' => null,
    ];
    
    protected $meta = [
        'prop' => [
            'type' => 'float'
        ],
    ];
}