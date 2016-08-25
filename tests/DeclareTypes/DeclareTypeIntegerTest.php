<?php
class DeclareTypeIntegerTest extends DtoTest\TestCase
{
    public function testWriteInteger()
    {
        $D = new DeclareTypeIntegerDto();
        $D->integer = 5;
        $this->assertEquals(5, $D->integer);
        
        $D->integer = '6x';
        $this->assertEquals(6, $D->integer);
        
        $D->set('integer', 7);
        $this->assertEquals(7, $D->integer);
        
        $D->set('integer', 'Not an Integer');
        $this->assertEquals(0, $D->integer);
        
        $D->set('integer', 'Not an Integer', true); // Bypass checks
        $this->assertEquals('Not an Integer', $D->integer);
    }
}
class DeclareTypeIntegerDto extends \Dto\Dto
{
    protected $template = [
        'integer' => null,
    ];
    
    protected $meta = [
        'integer' => [
            'type' => 'integer'
        ],
    ];
}