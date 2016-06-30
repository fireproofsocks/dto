<?php
class AutoTypeDetectionTest extends PHPUnit_Framework_Testcase
{
    /**
     * @ expectedException \Dto\Exceptions\InvalidLocationException
     */
    public function testInvalidExeption()
    {
        $D = new TestAutoTypeDetectionTestDto();
        $D->invalid = 'No no';
    }

    public function testWriteInteger()
    {
        $D = new TestAutoTypeDetectionTestDto();

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

    public function testWriteFloat()
    {
        $D = new TestAutoTypeDetectionTestDto();
        $D->float = 1.1;
        $this->assertEquals(1.1, $D->float);
    
        $D->float = 2;
        $this->assertEquals(2, $D->float);
        
        $D->set('float', 3.3);
        $this->assertEquals(3.3, $D->float);

        $D->float = 'Not a float';
        $this->assertEquals(0, $D->float);

        $D->set('float', 'Not a float', true); // Bypass checks
        $this->assertEquals('Not a float', $D->float);
    }
    
    public function testBoolean()
    {
        $D = new TestAutoTypeDetectionTestDto();
        $D->boolean = true;
        $this->assertTrue($D->boolean);

        $D->boolean = false;
        $this->assertFalse($D->boolean);

        $D->set('boolean', true);
        $this->assertTrue($D->boolean);

        $D->set('boolean', 'Not a bool', true); // Bypass checks
        $this->assertEquals('Not a bool', $D->boolean);
    }

    public function testScalar()
    {
        $D = new TestAutoTypeDetectionTestDto();

        $D->string = 'something';
        $this->assertEquals('something', $D->string);

        $D->string = true;
        $this->assertEquals('1', $D->string);

        $D->string = false;
        $this->assertEquals('', $D->string);

        $D->string = ['an', 'array'];
        $this->assertEquals('Array', $D->string);
    }

    public function testArray()
    {
        $D = new TestAutoTypeDetectionTestDto();
        $D->array = ['x','y','z'];
        $this->assertEquals(['x','y','z'], $D->array->toArray());
    }


    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testStingAtArrayLocation()
    {
        $D = new TestAutoTypeDetectionTestDto();
        $D->array = 'Not an Array';
    }

    public function testErraticArray()
    {
        $D = new TestAutoTypeDetectionTestDto();

        $erratic_index = [
            'a' => 'apple',
            'b' => 'banana',
            'c' => 'cherry'
        ];

        $D->array = $erratic_index;
        $this->assertEquals(['apple','banana','cherry'], $D->array->toArray());
    }

    public function testArrayAppend()
    {
        $D = new TestAutoTypeDetectionTestDto();
        $D->array = ['x','y'];
        $D->array[] = 'z';

        $this->assertEquals(['x','y','z'], $D->array->toArray());
    }

}

class TestAutoTypeDetectionTestDto extends \Dto\Dto
{
    protected $template = [
        'integer' => 0,
        'float' => 0.00, // i.e. number
        'boolean' => false,
        'string' => '', // strval - true/false converts to 1/0, arrays to "Array"
        'array' => [],
    ];
}