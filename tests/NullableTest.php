<?php
class NullableTest extends PHPUnit_Framework_Testcase
{
    public function testIndexesCanBeSetToNullWhenExplicitlyDeclaredAsNullable()
    {
        $D = new TestNullableTestDto();
        $D->my_nullable = 'x';
        $this->assertEquals('x', $D->my_nullable);
        $D->my_nullable = null;
        $this->assertNull($D->my_nullable);
    }
    
    public function testIndexesCannotBeSetToNullWhenExplicitlyDeclaredAsNotNullable()
    {
        $D = new TestNullableTestDto();
        $D->my_not_nullable = 'x';
        $this->assertEquals('x', $D->my_not_nullable);
        $D->my_not_nullable = null;
        $this->assertNotNull($D->my_not_nullable);
    }
    
    public function testUndeclaredIndexesAreImplicitlyNotNullable()
    {
        $D = new TestNullableTestDto();
        $D->my_undeclared = 'x';
        $this->assertEquals('x', $D->my_undeclared);
        $D->my_undeclared = null;
        $this->assertNotNull($D->my_undeclared);
    }
    
    
}

class TestNullableTestDto extends \Dto\Dto
{
    protected $template = [
        'my_nullable' => '',
        'my_not_nullable' => '',
        'my_undeclared' => '',
    ];

    protected $meta = [
        'my_nullable' => [
            'type' => 'scalar',
            'nullable' => true
        ],
        'my_not_nullable' => [
            'type' => 'string',
            'nullable' => false
        ],
    ];
}
