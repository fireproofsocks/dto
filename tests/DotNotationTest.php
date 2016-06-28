<?php
class DotNotationTest extends PHPUnit_Framework_Testcase
{
    public function testGet()
    {
        $D = new TestDotNotationTestDto();
        //print_r($D->mother->toArray()); exit;
        $this->assertEquals('Beth', $D->mother->firstname);
        $this->assertEquals('Beth', $D['mother']['firstname']);
        $this->assertEquals('Beth', $D->get('mother.firstname'));
        $this->assertEquals('Beth', $D->get('.mother.firstname'));
        $this->assertEquals('Beth', $D->mother->get('firstname'));
    }

    /**
     * @expectedException Dto\Exceptions\InvalidLocationException
     */
    public function testGetInvalid()
    {
        $D = new TestDotNotationTestDto();
        $result = $D->get('does.not.exist');
        //print var_dump($result); exit;
    }

    public function testSet()
    {
        $D = new TestDotNotationTestDto();
        $D->set('firstname', 'Snoopy');
        $this->assertEquals('Snoopy', $D->firstname);

        $D->mother->set('firstname', 'Margaret');
        $this->assertEquals('Margaret', $D->mother->firstname);
    }

    public function testForceSet2()
    {
        $D = new TestDotNotationTestDto();


        $D->mother->set('firstname', ['x','y','z'], true);
        $this->assertEquals(['x','y','z'], $D->mother->firstname);

    }

    public function testForceSet3()
    {
        $D = new TestDotNotationTestDto();

        $D->mother->set('firstname', ['a','b','c'], true);
        $this->assertEquals(['a','b','c'], $D->mother->firstname);
    }

    /**
     * @expectedException Dto\Exceptions\InvalidLocationException
     */
    public function testRegularSet()
    {
        $D = new TestDotNotationTestDto2();

        $D->firstname = ['x','y','z'];
        //print_r($D->toArray()); exit;
        //$this->assertEquals('Abby', $D->firstname, 'You should not be able to set a scalar value to an array without forcing it');

    }

}

class TestDotNotationTestDto extends \Dto\Dto
{
    protected $template = [
        'firstname' => 'Abby',
        'mother' => [
            'firstname' => 'Beth'
        ]
    ];
}

class TestDotNotationTestDto2 extends \Dto\Dto
{
    protected $template = [
        'firstname' => 'Abby'
    ];
}