<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class SetTest extends TestCase
{
    public function testSetRegularScalar()
    {
        $D = new \Dto\Dto();
        $D->set('x', 'xray');
        $this->assertEquals('xray', $D->x);
        $this->assertEquals('xray', $D['x']);
    
        $D->set('x', 'newval');
        $this->assertEquals('newval', $D->x);
        $this->assertEquals('newval', $D['x']);
    }
    
    public function testSetThruFilter()
    {
        $D = new \Dto\Dto([], ['x' => 0]);
        $D->set('x', 1);
        $this->assertEquals(1, $D->x);
        $D->set('x', 2.2);
        $this->assertEquals(2, $D->x);
        $D->set('x', '3cat');
        $this->assertEquals(3, $D->x);
        
    }
    
    public function testSetPropertyBypassFilters()
    {
        $D = new \Dto\Dto([], ['x' => 0]);
        $D->set('x', '3cat', true);
        $this->assertEquals('3cat', $D->x);
    }
    
    public function testSetRootNodeThruFilters()
    {
        $D = new SetDto([], ['x' => 0]);
        $D->set('.', ['x' => '3cat']);
        $this->assertEquals(3, $D->x);
    }
    
    public function testSetRootNodeByPassFilters()
    {
        $D = new SetDto([], ['x' => 0]);
        $D->set('.', ['x' => '3cat'], true);
        $this->assertEquals('3cat', $D->x);
    }
    
    public function testSetUsingNullIndexEquivalentToAppend()
    {
        $D = new \Dto\Dto([],[],['.' => ['type' => 'array']]);
        $D->set(null, 'a');
        $D->set(null, 'b');
        $D->set(null, 'c');
        $this->assertEquals(['a','b','c'], $D->toArray());
        
    }
}

class SetDto extends \Dto\Dto {
    
    // Simplified integer filtering here
    protected function filterNode($value, $index) {
        if (!is_array($value)) {
            return intval($value);
        }
        foreach ($value as $k => $v) {
            $value[$k] = intval($v);
        }
        return $value;
    }
    
}