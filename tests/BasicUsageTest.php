<?php
class BasicUsageTest extends PHPUnit_Framework_Testcase
{
    public function testInstantiation()
    {
        $D = new \Dto\Dto();
        $this->assertInstanceOf('Dto\Dto', $D);
    }

    public function testWhoAmI()
    {
        $D = new \Dto\Dto();
        $this->assertFalse(is_array($D), 'I am not an array');
        $this->assertTrue(is_object($D), 'I am an object');
        $this->assertTrue(is_array((array)$D), 'But I can be type-cast as an array');
    }

    public function testSimpleSetGet()
    {
        $D = new \Dto\Dto();

        $D->firstname = 'Nicole';
        $this->assertEquals('Nicole', $D->firstname);

    }

    public function testDeepSetGet()
    {
        $D = new \Dto\Dto();
        $D->mother->firstname = 'Debbie';
        $this->assertInstanceOf('Dto\Dto', $D);
        $this->assertInstanceOf('Dto\Dto', $D->mother);
        $this->assertEquals('Debbie', $D->mother->firstname);
    }

    public function testArraySetGet()
    {
        $D = new \Dto\Dto();

        $D['firstname'] = 'Durp';
        $this->assertEquals('Durp', $D['firstname']);
    }

    public function testDeepArraySetGet()
    {
        $D = new \Dto\Dto();

        $D['cousin']['firstname'] = 'Durp';
        $this->assertInstanceOf('Dto\Dto', $D['cousin']);
        $this->assertEquals('Durp', $D['cousin']['firstname']);
    }

    public function testMixAndMatch()
    {
        $D = new \Dto\Dto();
        $D['cousin']['firstname'] = 'Durp';
        $this->assertEquals('Durp', $D->cousin->firstname);
    }

    public function testBatchSet1()
    {
        $D = new \Dto\Dto();
        $D->mother = [
            'firstname' => 'Marge',
            'lastname' => 'Simpson'
        ];

        $this->assertEquals('Marge', $D->mother->firstname);
        $this->assertEquals('Simpson', $D->mother->lastname);

    }


    public function testBatchSet2()
    {
        $D = new \Dto\Dto();
        $D->mother = [
            'firstname' => 'Marge',
            'lastname' => 'Simpson',
            'mother' => [
                'firstname' => 'Doris'
            ]
        ];

        $this->assertEquals('Doris', $D->mother->mother->firstname);

    }

    public function testToArray()
    {
        $D = new \Dto\Dto();
        $D->firstname = 'Buzz';
        $D->mother->firstname = 'Margaret';
        $this->assertEquals(['firstname'=>'Buzz','mother'=>['firstname'=>'Margaret']], $D->toArray());
        $this->assertEquals(['firstname'=>'Margaret'], $D->mother->toArray());
    }

    public function testToJson()
    {
        $D = new \Dto\Dto();
        $D->firstname = 'Buzz';
        $D->mother->firstname = 'Margaret';
        $this->assertEquals(json_encode(['firstname'=>'Buzz','mother'=>['firstname'=>'Margaret']]), $D->toJson());
        $this->assertEquals(json_encode(['firstname'=>'Margaret']), $D->mother->toJson());
    }

    public function testToObject()
    {
        $D = new \Dto\Dto();
        $D->firstname = 'Buzz';
        $D->mother->firstname = 'Margaret';

        $obj2 = (object) ['firstname'=>'Margaret'];
        $obj = (object) ['firstname'=>'Buzz','mother'=>$obj2];

        $this->assertEquals($obj, $D->toObject());
        $this->assertEquals($obj2, $D->mother->toObject());
    }
    
    public function testAlterHashValuesByReference()
    {
        $D = new \Dto\Dto();
        
        $D->a = 'apple';
        $D->b = 'banana';
        $D->c = 'cherry';
        
        foreach($D as $k => &$v) {
            $v = $v . '1';
        }
        
        $this->assertEquals(['a'=>'apple1', 'b'=>'banana1', 'c'=>'cherry1'], $D->toArray());
    }
}