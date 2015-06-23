<?php

namespace Noxlogic\SerializerBundle\Tests\Service;

use Noxlogic\SerializerBundle\Service\Collection\CollectionRouting;

class CollectionRoutingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LogicException
     */
    public function testCall()
    {
        $cr = new CollectionRouting('my/site', array('id' => 1));
        $cr->generate();
    }

    public function testGenerate()
    {
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
