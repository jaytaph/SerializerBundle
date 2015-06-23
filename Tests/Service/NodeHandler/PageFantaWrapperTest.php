<?php

namespace Noxlogic\SerializerBundle\Tests\Service\NodeHandler;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Noxlogic\SerializerBundle\Service\Collection\CollectionRouting;
use Noxlogic\SerializerBundle\Service\NodeHandler\PagerFantaWrapper as PagerFantaWrapperHandler;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Noxlogic\SerializerBundle\Service\Collection\PagerFantaWrapper;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class MockRouter implements RouterInterface
{
    public function getRouteCollection()
    {
    }

    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        return "URL://$name/".http_build_query($parameters);
    }

    public function match($pathinfo)
    {
    }

    public function setContext(RequestContext $context)
    {
    }

    public function getContext()
    {
    }
}

class PagerFantaWrapperTest extends \PHPUnit_Framework_TestCase
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

    public function testIncorrectNode()
    {
        $context = new SerializerContext();

        $node = new PagerFantaWrapperHandler();
        $output = $node->handle('foobar', $this->serializer, $context);
        $this->assertNull($output);
    }

    public function testOutput()
    {
        $context = new SerializerContext();

        $mockRouter = new MockRouter();

        $routingCollection = new CollectionRouting('foobar', array('qf' => 1));
        $routingCollection->setRouter($mockRouter);

        $pager = new Pagerfanta(new ArrayAdapter(range('a', 'z')));
        $pfwc = new PagerFantaWrapper($pager, $routingCollection);

        $node = new PagerFantaWrapperHandler();
        $data = $node->handle($pfwc, $this->serializer, $context);

        $output = $data->compile();

        $this->assertEquals($output['count'], 26);
        $this->assertEquals($output['pages'], 3);
        $this->assertCount(10, $output['elements']);
        $this->assertEquals($output['_links']['self']['href'], 'URL://foobar/qf=1&page=1&limit=10');
        $this->assertEquals($output['_links']['next']['href'], 'URL://foobar/qf=1&page=2&limit=10');
        $this->assertArrayNotHasKey('prev', $output['_links']);

        $pager->setCurrentPage(2);
        $node = new PagerFantaWrapperHandler();
        $data = $node->handle($pfwc, $this->serializer, $context);
        $output = $data->compile();

        $this->assertEquals($output['count'], 26);
        $this->assertEquals($output['pages'], 3);
        $this->assertCount(10, $output['elements']);
        $this->assertEquals($output['_links']['self']['href'], 'URL://foobar/qf=1&page=2&limit=10');
        $this->assertEquals($output['_links']['next']['href'], 'URL://foobar/qf=1&page=3&limit=10');
        $this->assertEquals($output['_links']['prev']['href'], 'URL://foobar/qf=1&page=1&limit=10');

        $pager->setCurrentPage(3);
        $node = new PagerFantaWrapperHandler();
        $data = $node->handle($pfwc, $this->serializer, $context);
        $output = $data->compile();

        $this->assertEquals($output['count'], 26);
        $this->assertEquals($output['pages'], 3);
        $this->assertCount(6, $output['elements']);
        $this->assertEquals($output['_links']['self']['href'], 'URL://foobar/qf=1&page=3&limit=10');
        $this->assertEquals($output['_links']['prev']['href'], 'URL://foobar/qf=1&page=2&limit=10');
        $this->assertArrayNotHasKey('next', $output['_links']);
    }

    public function testOutputCustomized()
    {
        $context = new SerializerContext();

        $mockRouter = new MockRouter();

        $routingCollection = new CollectionRouting('foobar', array('qf' => 1));
        $routingCollection->setRouter($mockRouter);

        $pager = new Pagerfanta(new ArrayAdapter(range('a', 'z')));
        $pfwc = new PagerFantaWrapper($pager, $routingCollection, 'pagina', 'max', 'letters');

        $node = new PagerFantaWrapperHandler();
        $data = $node->handle($pfwc, $this->serializer, $context);

        $output = $data->compile();

        $this->assertEquals($output['count'], 26);
        $this->assertEquals($output['pages'], 3);
        $this->assertCount(10, $output['letters']);
        $this->assertArrayNotHasKey('elements', $output);
        $this->assertEquals($output['_links']['self']['href'], 'URL://foobar/qf=1&pagina=1&max=10');
        $this->assertEquals($output['_links']['next']['href'], 'URL://foobar/qf=1&pagina=2&max=10');
    }
}
