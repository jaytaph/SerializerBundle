<?php

namespace Noxlogic\SerializerBundle\Tests\Service\NodeHandler;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Noxlogic\SerializerBundle\Service\NodeHandler\Scalar;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

class ScalarTest extends \PHPUnit_Framework_TestCase
{
    /* @var Serializer */
    protected $serializer;

    /* @var Registry */
    protected $mockRegistry;

    /* @var RouterInterface */
    protected $mockRouter;

    /* @var ContainerInterface */
    protected $mockContainer;

    function setUp() {
        $this->mockRegistry = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->getMock();
        $this->mockRouter = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();
        $this->mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->disableOriginalConstructor()->getMock();

        $this->serializer = new Serializer($this->mockRegistry, $this->mockRouter, $this->mockContainer);
    }

    function testScalarNode() {
        $context = new SerializerContext();

        $node = new Scalar();
        $output = $node->handle('foobar', $this->serializer, $context);
        $this->assertEquals($output, 'foobar');

        $node = new Scalar();
        $output = $node->handle(1, $this->serializer, $context);
        $this->assertEquals($output, 1);

        $node = new Scalar();
        $output = $node->handle(true, $this->serializer, $context);
        $this->assertEquals($output, true);

        $node = new Scalar();
        $output = $node->handle(array('foobar'), $this->serializer, $context);
        $this->assertNull($output);
    }

}
