<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class GetMetaTest extends TestCase
{
    public function testThatMetaIsEmptyForNonExistentIndex()
    {
        $dto = new \Dto\Dto();
        
        // TODO: throw exception?
        $value = $this->callProtectedMethod($dto, 'getMeta', ['non-existent-index']);
        $this->assertEquals(['type'=>'unknown'], $value);
    }
}