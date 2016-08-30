<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class IsValidMetaKeyTest extends TestCase
{
    public function testIsValidMetaKey()
    {
        $dto = new \Dto\Dto();
        
        $this->assertFalse($this->callProtectedMethod($dto, 'isValidMetaKey', ['']));
        $this->assertTrue($this->callProtectedMethod($dto, 'isValidMetaKey', ['.']));
        $this->assertFalse($this->callProtectedMethod($dto, 'isValidMetaKey', ['cat..dog']));
        $this->assertFalse($this->callProtectedMethod($dto, 'isValidMetaKey', ['..catdog']));
        $this->assertTrue($this->callProtectedMethod($dto, 'isValidMetaKey', ['mother.father.dog']));
    }
}