<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class HashTest extends TestCase
{
    /**
     * @expectedException \Dto\Exceptions\InvalidLocationException
     */
    public function testThatYouCannotWriteToNonDefinedLocation()
    {
        $D = new DeclareTypeHashDto();
        $D->c = 'uh oh';
    }
}

class DeclareTypeHashDto extends \Dto\DtoStrict
{
    protected $template = [
        'a' => '',
        'b' => '',
    ];
    
    protected $meta = [
        '.' => [
            'type' => 'hash'
        ],
    ];
}