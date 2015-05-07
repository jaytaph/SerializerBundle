<?php

namespace Noxlogic\SerializerBundle\Tests\Service\Adapter;

use Noxlogic\SerializerBundle\Service\Adapter\Json;
use Noxlogic\SerializerBundle\Service\Data;

class JsonTest extends \PHPUnit_Framework_TestCase
{

    function testName()
    {
        $adapter = new Json();
        $this->assertEquals($adapter->getName(), 'json');
    }

    function testSupport()
    {
        $adapter = new Json();
        $this->assertTrue($adapter->isSupported('json'));
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

        $adapter = new Json();
        $response = $adapter->convert($data);

        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        $this->assertEquals($response->getContent(), '{"_links":{"l1":{"href":"http:\/\/www.google.com"}},"_embedded":{"e1":{"foo":"bar","_links":{"l2":{"href":"http:\/\/www.reddit.com"}},"_embedded":{"e2":{"deep":"down","_links":[]}}}}}');
    }

}
