<?php

namespace Noxlogic\SerializerBundle\Tests\Service\NodeHandler;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Noxlogic\SerializerBundle\Service\NodeHandler\DateTime;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    /* @var Serializer */
    protected $serializer;

    /* @var Registry */
    protected $mockRegistry;

    /* @var RouterInterface */
    protected $mockRouter;

    /* @var ContainerInterface */
    protected $mockContainer;

    public function setUp()
    {
        $this->mockRegistry = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->getMock();
        $this->mockRouter = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();
        $this->mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->disableOriginalConstructor()->getMock();

        $this->serializer = new Serializer($this->mockRegistry, $this->mockRouter, $this->mockContainer);
    }

    public function testIncorrectDoctrineEntityNode()
    {
        $context = new SerializerContext();

        $node = new DateTime();
        $output = $node->handle('foobar', $this->serializer, $context);
        $this->assertNull($output);

        $output = $node->handle(array(), $this->serializer, $context);
        $this->assertNull($output);

        $output = $node->handle(new \StdClass(), $this->serializer, $context);
        $this->assertNull($output);
    }

    public function testDateTimeNode()
    {
        $context = new SerializerContext();

        $node = new DateTime();
        $data = $node->handle(new \DateTime('2015-07-09 12:34:56', new \DateTimeZone('Europe/Berlin')), $this->serializer, $context);
        $this->assertEquals('2015-07-09T12:34:56+0200', $data);

        $node = new DateTime();
        $data = $node->handle(new \DateTime('2015-07-09 12:34:56', new \DateTimeZone('America/New_York')), $this->serializer, $context);
        $this->assertEquals('2015-07-09T12:34:56-0400', $data);
    }
}
