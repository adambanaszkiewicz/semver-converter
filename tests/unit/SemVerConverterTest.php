<?php

use Requtize\SemVerConverter\SemVerConverter;

class SemVerConverterTest extends \PHPUnit\Framework\TestCase
{
    public function testConvertSimple()
    {
        $result = (new SemVerConverter)->convert('1.2.3');
        $this->assertEquals('1002003', $result[0]['from'][0]);
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
        $this->assertEquals('1', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('0.0.01');
        $this->assertEquals('1', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('0.0.001');
        $this->assertEquals('1', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('0.0.12');
        $this->assertEquals('12', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('0.0.012');
        $this->assertEquals('12', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('0.0.123');
        $this->assertEquals('123', $result[0]['from'][0]);
    }

    public function testConvertMinor()
    {
        $result = (new SemVerConverter)->convert('0.1.0');
        $this->assertEquals('1000', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('0.01.0');
        $this->assertEquals('1000', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('0.001.0');
        $this->assertEquals('1000', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('0.12.0');
        $this->assertEquals('12000', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('0.012.0');
        $this->assertEquals('12000', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('0.123.0');
        $this->assertEquals('123000', $result[0]['from'][0]);
    }

    public function testConvertMajor()
    {
        $result = (new SemVerConverter)->convert('1.0.0');
        $this->assertEquals('1000000', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('01.0.0');
        $this->assertEquals('1000000', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('001.0.0');
        $this->assertEquals('1000000', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('12.0.0');
        $this->assertEquals('12000000', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('012.0.0');
        $this->assertEquals('12000000', $result[0]['from'][0]);

        $result = (new SemVerConverter)->convert('123.0.0');
        $this->assertEquals('123000000', $result[0]['from'][0]);
    }

    public function testConvertTwoSegments()
    {
        $result = (new SemVerConverter)->convert('1.2');
        $this->assertEquals('1002000', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('11.22');
        $this->assertEquals('11022000', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('111.222');
        $this->assertEquals('111222000', $result[0]['from'][0]);
    }

    public function testConvertOneSegment()
    {
        $result = (new SemVerConverter)->convert('1');
        $this->assertEquals('1000000', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('11');
        $this->assertEquals('11000000', $result[0]['from'][0]);
        $result = (new SemVerConverter)->convert('111');
        $this->assertEquals('111000000', $result[0]['from'][0]);
    }

    public function testEncodeSimple()
    {
        $result = (new SemVerConverter)->encode(100200300);
        $this->assertEquals('1.2.3', $result);
        $result = (new SemVerConverter)->encode(200300);
        $this->assertEquals('0.2.3', $result);
        $result = (new SemVerConverter)->encode(300);
        $this->assertEquals('0.0.3', $result);

        $result = (new SemVerConverter)->encode(100000000);
        $this->assertEquals('1.0.0', $result);
        $result = (new SemVerConverter)->encode(10000000);
        $this->assertEquals('1.0.0', $result);
        $result = (new SemVerConverter)->encode(1000000);
        $this->assertEquals('1.0.0', $result);
        $result = (new SemVerConverter)->encode(100000);
        $this->assertEquals('0.1.0', $result);
        $result = (new SemVerConverter)->encode(10000);
        $this->assertEquals('0.1.0', $result);
        $result = (new SemVerConverter)->encode(1000);
        $this->assertEquals('0.1.0', $result);
        $result = (new SemVerConverter)->encode(100);
        $this->assertEquals('0.0.1', $result);
        $result = (new SemVerConverter)->encode(10);
        $this->assertEquals('0.0.1', $result);
        $result = (new SemVerConverter)->encode(1);
        $this->assertEquals('0.0.1', $result);

        $result = (new SemVerConverter)->encode(123456789);
        $this->assertEquals('123.456.789', $result);
        $result = (new SemVerConverter)->encode(12345678);
        $this->assertEquals('12.345.678', $result);
        $result = (new SemVerConverter)->encode(1234567);
        $this->assertEquals('1.234.567', $result);
        $result = (new SemVerConverter)->encode(123456);
        $this->assertEquals('0.123.456', $result);
        $result = (new SemVerConverter)->encode(12345);
        $this->assertEquals('0.12.345', $result);
        $result = (new SemVerConverter)->encode(1234);
        $this->assertEquals('0.1.234', $result);
        $result = (new SemVerConverter)->encode(123);
        $this->assertEquals('0.0.123', $result);
        $result = (new SemVerConverter)->encode(12);
        $this->assertEquals('0.0.12', $result);
        $result = (new SemVerConverter)->encode(1);
        $this->assertEquals('0.0.1', $result);
    }

    public function testEncodeZero()
    {
        $result = (new SemVerConverter)->encode(0);
        $this->assertEquals('0.0.0', $result);

        $result = (new SemVerConverter)->encode('00000000');
        $this->assertEquals('0.0.0', $result);
    }
}
