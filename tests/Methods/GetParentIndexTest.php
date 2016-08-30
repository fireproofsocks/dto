<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class GetParentIndexTest extends TestCase
{
    public function testThatDepthOfTwoResolvesToDepthOfOne()
    {
        $dto = new \Dto\Dto();
        $value = $this->callProtectedMethod($dto, 'getParentIndex', ['.something.else']);
        $this->assertEquals('.something', $value);
    }
    
    public function testThatDepthOfThreeResolvesToDepthOfTwo()
    {
        $dto = new \Dto\Dto();
        $value = $this->callProtectedMethod($dto, 'getParentIndex', ['.something.else.yet']);
        $this->assertEquals('.something.else', $value);
    }
    
    public function testThatRootResolvesToRoot()
    {
        $dto = new \Dto\Dto();
        $value = $this->callProtectedMethod($dto, 'getParentIndex', ['.']);
        $this->assertEquals('.', $value);
    }
    
    public function testThatDepthOfOneResolvesToRoot()
    {
        $dto = new \Dto\Dto();
        $value = $this->callProtectedMethod($dto, 'getParentIndex', ['.something']);
        $this->assertEquals('.', $value);
    }
}