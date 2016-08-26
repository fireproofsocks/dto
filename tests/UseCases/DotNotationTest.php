<?php
class DotNotationTest extends DtoTest\TestCase
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
        //exit;
        $D->set('firstname', 'Snoopy');
        $this->assertEquals('Snoopy', $D->firstname);
//print_r($D->mother); exit;
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
     *
     */
    public function testRegularSet()
    {
        $D = new \Dto\Dto();

        $D->firstname = ['x','y','z'];

        $this->assertEquals('Array', $D->firstname);

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