<?php

namespace DtoTest\DeclareTypes;

use Dto\Dto;
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
    
    /**
     * @expectedException \Dto\Exceptions\InvalidMetaKeyException
     */
    public function testExceptionThrownForInvalidMetaKey()
    {
        $dto = new NormalizeMetaTestDto();
    
        $meta = [
            '..' => 'double-dots are not allowed',
        ];
        
        $this->callProtectedMethod($dto, 'normalizeMeta', [$meta]);
    }
}

class NormalizeMetaTestDto extends Dto
{
    protected function isValidMetaKey($key)
    {
        false;
    }
}