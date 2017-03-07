<?php
namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class EmailTest extends TestCase
{
    public function testEmail()
    {
        $email = new Email('somebody@test.com');

        $this->assertEquals('somebody@test.com', $email->toScalar());
    }

    public function testEmailRestricted()
    {
        $email = new EmailRestricted('somebody@test.com');

        $this->assertEquals('somebody@test.com', $email->toScalar());
    }
}

class Email extends Dto
{
    protected $schema = [
        'type' => 'string',
        'format' => 'email'
    ];
}

class EmailRestricted extends Dto
{
    protected $schema = [
        'type' => 'string',
        'format' => 'email',
        'pattern' => '@test\.com$',
    ];
}