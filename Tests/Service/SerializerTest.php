<?php

namespace Noxlogic\SerializerBundle\Tests\Service;

use Noxlogic\SerializerBundle\Service\Adapter\Html;
use Noxlogic\SerializerBundle\Service\Adapter\Xml;
use Noxlogic\SerializerBundle\Service\Serializer;

class SerializerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Noxlogic\SerializerBundle\Service\Serializer
     */
    protected $serializer;

    function setUp() {
        $this->mockRegistry = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->getMock();
        $this->mockRouter = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();

        $this->serializer = new Serializer($this->mockRegistry, $this->mockRouter);
    }


    function testAdapters() {
        $this->assertFalse($this->serializer->isAdapterLoaded('html'));
        $this->serializer->addAdapter(new Html());
        $this->assertTrue($this->serializer->isAdapterLoaded('html'));
        $this->assertNotNull($this->serializer->getAdapter('html'));


        $this->serializer->removeAdapter('html');
        $this->assertFalse($this->serializer->isAdapterLoaded('html'));


        $this->serializer->addAdapter(new Xml());
        $this->serializer->addAdapter(new Html());
        $this->serializer->clearAdapters();
        $this->assertFalse($this->serializer->isAdapterLoaded('html'));
        $this->assertFalse($this->serializer->isAdapterLoaded('xml'));

    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    function testGetAdapterFailure() {
        $this->serializer->getAdapter('foobar');
    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    function testRemoveAdapterFailure() {
        $this->serializer->removeAdapter('foobar');
    }

}
