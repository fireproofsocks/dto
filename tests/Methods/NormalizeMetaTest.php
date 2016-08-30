<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class NormalizeMetaTest extends TestCase
{
    public function testThatDotsArePrepended()
    {
        $dto = new \Dto\Dto();

        $meta = [
            'firstname' => 'x',
            '.lastname' => 'y',
            'mother.firstname' => 'a',
        ];
        
        $normalized = [
            '.firstname' => 'x',
            '.lastname' => 'y',
            '.mother.firstname' => 'a',
        ];

        $value = $this->callProtectedMethod($dto, 'normalizeMeta', [$meta]);
        $this->assertEquals($normalized, $value);
    }
}