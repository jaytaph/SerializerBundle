<?php

namespace Noxlogic\SerializerBundle\Tests\Service\NodeHandler;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Noxlogic\SerializerBundle\Service\NodeHandler\Collection;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Noxlogic\SerializerBundle\Tests\Service\NodeHandler\Fixtures\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

class CollectionTest extends \PHPUnit_Framework_TestCase
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


    function testIncorrectDoctrineEntityNode()
    {
        $context = new SerializerContext();

        $node = new Collection();
        $output = $node->handle('foobar', $this->serializer, $context);
        $this->assertNull($output);
    }

    function testOutput()
    {
        $context = new SerializerContext();

        $collection = new ArrayCollection(array('foo', 'bar', 'baz'));

        $node = new Collection();
        $data = $node->handle($collection, $this->serializer, $context);

        $output = $data->compile();

        $this->assertEquals($output['count'], 3);
        $this->assertEquals($output['elements'][0], 'foo');
        $this->assertEquals($output['elements'][1], 'bar');
        $this->assertEquals($output['elements'][2], 'baz');
        $this->assertArrayNotHasKey('_links', $output);
    }

    function testOutputWithArray()
    {
        $context = new SerializerContext();

        $collection = array('foo', 'bar', 'baz');

        $node = new Collection();
        $data = $node->handle($collection, $this->serializer, $context);

        $output = $data->compile();

        $this->assertEquals($output['count'], 3);
        $this->assertEquals($output['elements'][0], 'foo');
        $this->assertEquals($output['elements'][1], 'bar');
        $this->assertEquals($output['elements'][2], 'baz');
        $this->assertArrayNotHasKey('_links', $output);
    }

    function testOutputWithTraversable()
    {
        $context = new SerializerContext();

        $collection = new \DatePeriod(
            new \DateTime('01-01-2015', new \DateTimeZone('CET')),
            new \DateInterval('P1M'),
            new \DateTime('01-01-2016', new \DateTimeZone('CET'))
        );

        $node = new Collection();
        $data = $node->handle($collection, $this->serializer, $context);

        $output = $data->compile();

        $this->assertEquals($output['count'], 12);
        $this->assertEquals($output['elements'][0], '2015-01-01T00:00:00+0100');
        $this->assertEquals($output['elements'][4], '2015-05-01T00:00:00+0200');
        $this->assertEquals($output['elements'][7], '2015-08-01T00:00:00+0200');
        $this->assertArrayNotHasKey('_links', $output);
    }


    function testOutputWithComplexElements()
    {
        $context = new SerializerContext();

        $user1 = new User();
        $user1->setFirstname('john');
        $user1->setLastname('doe');

        $user2 = new User();
        $user2->setFirstname('mr');
        $user2->setLastname('black');

        $collection = new ArrayCollection(array($user1, 'bar', $user2));

        $node = new Collection();
        $data = $node->handle($collection, $this->serializer, $context);

        $output = $data->compile();

        $this->assertEquals($output['count'], 3);
        $this->assertEquals($output['elements'][0]['fullname'], 'john doe');
        $this->assertArrayHasKey('_links', $output['elements'][0]);
        $this->assertEquals($output['elements'][1], 'bar');
        $this->assertEquals($output['elements'][2]['fullname'], 'mr black');
        $this->assertArrayHasKey('_links', $output['elements'][2]);
        $this->assertArrayNotHasKey('_links', $output);
    }

}
