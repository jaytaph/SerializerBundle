<?php

namespace Noxlogic\SerializerBundle\Tests\Service;

use Noxlogic\SerializerBundle\Service\OutputAdapter\Html;
use Noxlogic\SerializerBundle\Service\OutputAdapter\Xml;
use Noxlogic\SerializerBundle\Service\Serializer;

class SerializerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Serializer
     */
    protected $serializer;

    function setUp() {
        $this->mockRegistry = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->getMock();
        $this->mockRouter = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();
        $this->mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->disableOriginalConstructor()->getMock();

        $this->serializer = new Serializer($this->mockRegistry, $this->mockRouter, $this->mockContainer);
    }


    function testAdapters() {
        $this->assertFalse($this->serializer->isOutputAdapterLoaded('html'));
        $this->serializer->addOutputAdapter(new Html());
        $this->assertTrue($this->serializer->isOutputAdapterLoaded('html'));
        $this->assertNotNull($this->serializer->getOutputAdapter('html'));


        $this->serializer->removeOutputAdapter('html');
        $this->assertFalse($this->serializer->isOutputAdapterLoaded('html'));


        $this->serializer->addOutputAdapter(new Xml());
        $this->serializer->addOutputAdapter(new Html());
        $this->serializer->clearOutputAdapters();
        $this->assertFalse($this->serializer->isOutputAdapterLoaded('html'));
        $this->assertFalse($this->serializer->isOutputAdapterLoaded('xml'));

    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    function testGetOutputAdapterFailure() {
        $this->serializer->getOutputAdapter('foobar');
    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    function testRemoveOutputAdapterFailure() {
        $this->serializer->removeOutputAdapter('foobar');
    }

}
