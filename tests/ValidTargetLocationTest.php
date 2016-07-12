<?php
class ValidTargetLocationTest extends PHPUnit_Framework_Testcase
{
    /**
     * @expectedException \Dto\Exceptions\InvalidLocationException
     */
    public function test1()
    {
        $D = new TestValidTargetLocationTestDto();
        $D->invalid = 'bork';
    }

    public function test2()
    {
        $D = new TestValidTargetLocationTestDto();
        $D->myhash->invalid = 'ok';
    }
}

class TestValidTargetLocationTestDto extends \Dto\Dto
{
    protected $template = [
        'myhash' => null,
    ];
    
    protected $meta = [
        'myhash' => [
            'type' => 'hash',
            'keys' => '/A-Z/', // ???
        ]
    ];

    // As a named function?
    // What if the index has spaces or dashes?
    public function myhashKeys($index)
    {

    }
}