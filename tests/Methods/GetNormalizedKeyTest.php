<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class GetNormalizedKeyTest extends TestCase
{
    public function testGetNormalizedKey()
    {
        $dto = new \Dto\Dto();
        
        $this->assertEquals('.cat', $this->callProtectedMethod($dto, 'getNormalizedKey', ['cat']));
        $this->assertEquals('.cat', $this->callProtectedMethod($dto, 'getNormalizedKey', ['.cat']));
        $this->assertEquals('.cat', $this->callProtectedMethod($dto, 'getNormalizedKey', ['.cat.']));
    }
}