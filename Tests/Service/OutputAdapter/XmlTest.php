<?php

namespace Noxlogic\SerializerBundle\Tests\Service\OutputAdapter;

use Noxlogic\SerializerBundle\Service\OutputAdapter\Xml;
use Noxlogic\SerializerBundle\Service\Data;

class XmlTest extends \PHPUnit_Framework_TestCase
{

    function testName()
    {
        $adapter = new Xml();
        $this->assertEquals($adapter->getName(), 'xml');
    }

    function testSupport()
    {
        $adapter = new Xml();
        $this->assertTrue($adapter->isSupported('xml'));
        $this->assertFalse($adapter->isSupported('json'));
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

        $adapter = new Xml();
        $response = $adapter->convert($data);

        // @TODO: Check for decent HTML output
        
        $this->assertEquals($response->headers->get('Content-Type'), 'text/xml');
    }

}
