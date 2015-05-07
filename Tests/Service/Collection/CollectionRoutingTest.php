<?php

namespace Noxlogic\SerializerBundle\Tests\Service;


use Noxlogic\SerializerBundle\Service\Collection\CollectionRouting;
use Noxlogic\SerializerBundle\Service\SerializerContext;

class CollectionRoutingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \LogicException
     */
    function testCall()
    {
        $cr = new CollectionRouting('my/site', array('id' => 1));
        $cr->generate();
    }

    function testGenerate() {
        $router = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')
                ->getMock();
        $router->expects($this->once())
            ->method('generate')
            ->with('my/site', array('id' => 1, 'foo' => 'bar'));

        $cr = new CollectionRouting('my/site', array('id' => 1));
        $cr->setRouter($router);
        $cr->generate(array('foo' => 'bar'));
    }

}
