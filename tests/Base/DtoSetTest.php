<?php
namespace DtoTest\Base;

use Dto\Dto;
use Dto\DtoInterface;

class DtoSetTest extends DtoTestCase
{

    public function testInstantiation()
    {
        $dto = new Dto(null, null, $this->getMockRegulator([]));
        $this->assertInstanceOf(DtoInterface::class, $dto);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testSetOnScalarDtoThrowsException()
    {
        $dto = new Dto(null, null, $this->getMockRegulator(null, 'scalar'));
        $dto->set('does-not-exist', 'some-value');
    }

    public function testSetOnObjectDto()
    {
        $dto = new Dto(null, null, $this->getMockRegulator([], 'object'));
        $dto->set('a', 'apple');

        $this->assertEquals(['a' => 'apple'], $dto->toArray());
    }
}