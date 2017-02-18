<?php
namespace DtoTest\Format;

use Dto\Validators\Types\String\FormatValidator;
use Dto\Validators\Types\String\FormatValidatorInterface;
use DtoTest\TestCase;

class FormatValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new FormatValidator();
    }

    public function testInstantiation()
    {
        $f = $this->getInstance();
        $this->assertInstanceOf(FormatValidatorInterface::class, $f);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidFormatException
     */
    public function testCheckThrowsExceptionOnInvalidDataType()
    {
        $f = $this->getInstance();
        $f->check(['not-a-scalar'], 'ignored');
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidFormatException
     */
    public function testCheckThrowsExceptionOnUnrecognizedFormat()
    {
        $f = $this->getInstance();
        $f->check('not-in-the-spec', 'ignored');
    }

    public function testCheck()
    {
        $f = $this->getInstance();
        $result = $f->check('email', 'abcd@mail.com');
        $this->assertEquals('abcd@mail.com', $result);
    }

    public function testAsDateTime()
    {
        $f = $this->getInstance();
        $input = '2005-08-15T15:52:01+00:00';
        $result = $f->asDateTime($input);
        $this->assertEquals($input, $result);
    }

    public function testAsDateTimeReturnsFalseWhenDateTimeIsNotInRFC3339Format()
    {
        $f = $this->getInstance();
        $input = '2012-12-31 11:59:01';
        $result = $f->asDateTime($input);
        $this->assertFalse($result);
    }

    public function testAsEmail()
    {
        $f = $this->getInstance();
        $result = $f->asEmail('abcd@mail.com');
        $this->assertEquals('abcd@mail.com', $result);
    }

    public function testAsEmailReturnsFalseWhenStringIsNotAValidEmailFormat()
    {
        $f = $this->getInstance();
        $result = $f->asEmail('not an email');
        $this->assertFalse($result);
    }
    
    public function testAsHostname()
    {
        $f = $this->getInstance();
        $result = $f->asHostname('mail.com');
        $this->assertEquals('mail.com', $result);
    }

    public function testAsHostnameReturnsFalseWhenInvalidHostnameEncountered()
    {
        $f = $this->getInstance();
        $result = $f->asHostname('mail com');
        $this->assertFalse($result);
    }

    public function testAsIpv4()
    {
        $f = $this->getInstance();
        $result = $f->asIpv4('127.0.0.1');
        $this->assertEquals('127.0.0.1', $result);
    }

    public function testAsIpv4ReturnsFalseOnInvalidIpV4()
    {
        $f = $this->getInstance();
        $result = $f->asIpv4('127.0.1');
        $this->assertFalse($result);
    }

    public function testAsIpv6()
    {
        $f = $this->getInstance();
        $result = $f->asIpv6('::1');
        $this->assertEquals('::1', $result);
    }

    public function testAsIpv6ReturnsFalseOnInvalidIpV6()
    {
        $f = $this->getInstance();
        $result = $f->asIpv6('127.0.0.1');
        $this->assertFalse($result);
    }

    public function testAsUri()
    {
        $f = $this->getInstance();
        $input = 'ftp://ftp.is.co.za.example.org/rfc/rfc1808.txt';
        $result = $f->asUri($input);
        $this->assertEquals($input, $result);
    }

    public function testAsUriReturnsFalseOnInvalidUrl()
    {
        $f = $this->getInstance();
        $input = '/rfc/rfc1808.txt';
        $result = $f->asUri($input);
        $this->assertFalse($result);
    }

    public function testAsUriRef()
    {
        $f = $this->getInstance();
        $input = '/rfc/rfc1808.txt';
        $result = $f->asUriRef($input);
        $this->assertEquals($input, $result);
    }

    public function testAsUriRefReturnsFalseOnInvalidUrl()
    {
        $f = $this->getInstance();
        $input = 'not a valid ref';
        $result = $f->asUriRef($input);
        $this->assertFalse($result);
    }
}