<?php
class DeclareTypeScalarTest extends \DtoTest\TestCase
{
    public function test()
    {
        $D = new DeclareTypeScalarDto();
        
        $this->assertEquals('', $D->prop);
        
        $D->prop = 5;
        $this->assertEquals('5', $D->prop);
        
        $D->prop = '6x';
        $this->assertEquals('6x', $D->prop);
        
        $D->set('prop', null);
        $this->assertEquals('', $D->prop);
        
        $D->set('prop', 7.1000);
        $this->assertEquals('7.1000', $D->prop);
        
        $D->set('prop', 123, true); // Bypass checks
        $this->assertEquals(123, $D->prop);
    }
}


class DeclareTypeScalarDto extends \Dto\Dto
{
    protected $template = [
        'prop' => null,
    ];
    
    protected $meta = [
        'prop' => [
            'type' => 'scalar'
        ],
    ];
}