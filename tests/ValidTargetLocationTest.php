<?php
class ValidTargetLocationTest extends PHPUnit_Framework_Testcase
{
    /**
     * @expectedException \Dto\Exceptions\InvalidLocationException
     */
    public function test1()
    {
        $D = new TestValidTargetLocationTestDto();
        $D->invalid1 = 'bork';
    }

    public function testInvalid()
    {
        $D = new TestValidTargetLocationTestDto();
        $D->myhash->invalid2 = 'ok';
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
            'keys_regex' => '/A-Z/', // ???
            'keys_allowed' => [], // ???
        ]
    ];

    // As a named function?
    // What if the index has spaces or dashes?
    // How about a function that accepts the parent node (e.g. "myhash") and the proposed target $index
//    protected function isValidTargetLocation($index)
//    {
//        if (!$result = parent::isValidTargetLocation($index)) {
//            print 'false.... ??? @->'.$index; exit;
//            return false;
//        }
//
//        return true;
//    }
}