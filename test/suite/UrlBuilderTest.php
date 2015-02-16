<?php
namespace Icecave\Sid;

use Icecave\Chrono\DateTime;
use PHPUnit_Framework_TestCase;

class UrlBuilderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new UrlBuilder('<key>');
    }

    public function testBuild()
    {
        $this->assertEquals(
            'http://xml.sportsdirectinc.com/path/to/feed?apiKey=%3Ckey%3E',
            $this->builder->build(
                '/path/to/feed'
            )
        );
    }

    public function testBuildWithParameters()
    {
        $this->assertEquals(
            'http://xml.sportsdirectinc.com/path/to/feed?apiKey=%3Ckey%3E&int=12&str=Hi%21&obj=2014-01-02T03%3A04%3A05%2B00%3A00',
            $this->builder->build(
                '/path/to/feed',
                [
                    'int' => 12,
                    'str' => 'Hi!',
                    'obj' => new DateTime(2014, 1, 2, 3, 4, 5)
                ]
            )
        );
    }

    public function testBuildWithInvalidResource()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Resource must begin with a slash.'
        );

        $this->builder->build('foo');
    }
}
