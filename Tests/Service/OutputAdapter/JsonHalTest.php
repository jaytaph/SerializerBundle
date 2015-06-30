<?php

namespace Noxlogic\SerializerBundle\Tests\Service\OutputAdapter;

use Noxlogic\SerializerBundle\Service\OutputAdapter\JsonHal;
use Noxlogic\SerializerBundle\Service\Data;

class JsonHalTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $adapter = new JsonHal();
        $this->assertEquals($adapter->getName(), 'json');
    }

    public function testSupport()
    {
        $adapter = new JsonHal();
        $this->assertTrue($adapter->isSupported('json'));
        $this->assertFalse($adapter->isSupported('xml'));
    }

    public function testConvert()
    {
        $data2 = Data::create()
            ->addState('deep', 'down')
        ;

        $data1 = Data::create()
            ->addState('foo', 'bar')
            ->addLink('l2', 'http://www.reddit.com')
            ->addEmbedded('e2', $data2)
        ;
        $data = Data::create()
            ->addLink('l1', 'http://www.google.com')
            ->addEmbedded('e1', $data1)
        ;

        $adapter = new JsonHal();
        $response = $adapter->convert($data);

        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
        $this->assertEquals($response->getContent(), '{"_links":{"l1":{"href":"http:\/\/www.google.com"}},"_embedded":{"e1":{"foo":"bar","_links":{"l2":{"href":"http:\/\/www.reddit.com"}},"_embedded":{"e2":{"deep":"down","_links":[]}}}}}');
    }
}
