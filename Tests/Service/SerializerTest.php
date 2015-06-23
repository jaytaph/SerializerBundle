<?php

namespace Noxlogic\SerializerBundle\Tests\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Noxlogic\SerializerBundle\Service\Data;
use Noxlogic\SerializerBundle\Service\OutputAdapter\Html;
use Noxlogic\SerializerBundle\Service\OutputAdapter\JsonHal;
use Noxlogic\SerializerBundle\Service\OutputAdapter\Xml;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class SerializerTest extends \PHPUnit_Framework_TestCase
{

    /* @var Serializer */
    protected $serializer;

    /* @var Registry */
    protected $mockRegistry;

    /* @var RouterInterface */
    protected $mockRouter;

    /* @var ContainerInterface */
    protected $mockContainer;

    function setUp()
    {
        $this->mockRegistry = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->getMock();
        $this->mockRouter = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();
        $this->mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->disableOriginalConstructor()->getMock();

        $this->serializer = new Serializer($this->mockRegistry, $this->mockRouter, $this->mockContainer);
    }


    function testAdapters()
    {
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
    function testGetOutputAdapterFailure()
    {
        $this->serializer->getOutputAdapter('foobar');
    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    function testRemoveOutputAdapterFailure()
    {
        $this->serializer->removeOutputAdapter('foobar');
    }

    function testCreateResponse()
    {
        $this->serializer->addOutputAdapter(new JsonHal());

        $data = Data::create()
                ->addState('foo', 'bar')
                ->addLink('self', 'https://www.google.com');
        $response = $this->serializer->createResponse($data, 'json-hal');

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
    }

    /**
     * @expectedException \LogicException
     */
    function testSerializerWithUnknownNode()
    {
        $context = new SerializerContext();
        $this->serializer->serialize(new \DomDocument(), $context);
    }

    function testSerializerWithSimpleScalar()
    {
        $context = new SerializerContext();
        $output = $this->serializer->serialize('foobar', $context);

        $this->assertEquals($output, 'foobar');
    }

    function testSerializedWithDoctrineEntity()
    {
        $context = new SerializerContext();
        $output = $this->serializer->serialize('foobar', $context);

        $this->assertEquals($output, 'foobar');
    }

    function testDependencies()
    {
        $this->assertEquals($this->serializer->getContainer(), $this->mockContainer);
        $this->assertEquals($this->serializer->getRouter(), $this->mockRouter);
    }

}
