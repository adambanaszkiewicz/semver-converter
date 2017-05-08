<?php

use Requtize\SemVerConverter\SemVerConverter;

class SemVerConverterTest extends PHPUnit_Framework_TestCase
{
    public function testConvertSimple()
    {
        $result = (new SemVerConverter)->convert('1.2.3');
        $this->assertEquals('100200300', $result[0]['from'][0]);
    }

    public function testConvertZeros()
    {
        $result = (new SemVerConverter)->convert('0.0.0');
        $this->assertEquals('0', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('0.0');
        $this->assertEquals('0', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('0');
        $this->assertEquals('0', $result[0]['from'][0]);
    }

    public function testConvertPatch()
    {
        $result = (new SemVerConverter)->convert('0.0.1');
        $this->assertEquals('100', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('0.0.12');
        $this->assertEquals('120', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('0.0.123');
        $this->assertEquals('123', $result[0]['from'][0]);
    }

    public function testConvertMinor()
    {
        $result = (new SemVerConverter)->convert('0.1.0');
        $this->assertEquals('100000', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('0.12.0');
        $this->assertEquals('120000', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('0.123.0');
        $this->assertEquals('123000', $result[0]['from'][0]);
    }

    public function testConvertMajor()
    {
        $result = (new SemVerConverter)->convert('1.0.0');
        $this->assertEquals('100000000', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('12.0.0');
        $this->assertEquals('120000000', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('123.0.0');
        $this->assertEquals('123000000', $result[0]['from'][0]);
    }

    public function testConvertTwoSegments()
    {
        $result = (new SemVerConverter)->convert('1.2');
        $this->assertEquals('100200000', $result[0]['from'][0]);
    }

    public function testConvertOneSegment()
    {
        $result = (new SemVerConverter)->convert('1');
        $this->assertEquals('100000000', $result[0]['from'][0]);
    }

    public function testEncodeSimple()
    {
        $result = (new SemVerConverter)->encode(100200300);
        $this->assertEquals('1.2.3', $result);
    }

    public function testEncodeZero()
    {
        $result = (new SemVerConverter)->encode(0);
        $this->assertEquals('0.0.0', $result);

        $result = (new SemVerConverter)->encode('00000000');
        $this->assertEquals('0.0.0', $result);
    }
}
