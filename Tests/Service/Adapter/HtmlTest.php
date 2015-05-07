<?php

namespace Noxlogic\SerializerBundle\Tests\Service\Adapter;

use Noxlogic\SerializerBundle\Service\Adapter\Html;
use Noxlogic\SerializerBundle\Service\Data;

class HtmlTest extends \PHPUnit_Framework_TestCase
{

    function testName()
    {
        $adapter = new Html();
        $this->assertEquals($adapter->getName(), 'html');
    }

    function testSupport()
    {
        $adapter = new Html();
        $this->assertTrue($adapter->isSupported('html'));
        $this->assertFalse($adapter->isSupported('xml'));
    }

    function testConvert()
    {
        $data2 = Data::create();
        $data2->addState('deep', 'down');
        $data1 = Data::create();
        $data1->addState('foo', 'bar');
        $data1->addLink('l2', 'http://www.reddit.com');
        $data1->addEmbedded('e2', $data2);
        $data = Data::create();
        $data->addLink('l1', 'http://www.google.com');
        $data->addEmbedded('e1', $data1);

        $adapter = new Html();
        $response = $adapter->convert($data);


        // @TODO: Check for decent HTML output


        $this->assertEquals($response->headers->get('Content-Type'), 'text/html');
    }

}
