<?php

namespace Noxlogic\SerializerBundle\Tests\Service;


use Noxlogic\SerializerBundle\Service\Collection\CollectionRouting;
use Noxlogic\SerializerBundle\Service\Collection\PagerFantaWrapper;

class PagerFantaWrapperTest extends \PHPUnit_Framework_TestCase
{

    function setUp() {
        $this->pagerFantaMock = $this->getMockBuilder('Pagerfanta\Pagerfanta')->disableOriginalConstructor()->getMock();
        $this->cr = $this->getMockBuilder('Noxlogic\SerializerBundle\Service\Collection\CollectionRouting')->disableOriginalConstructor()->getMock();

        $this->pfw = new PagerFantaWrapper($this->pagerFantaMock, $this->cr, 'page', 'limit');
    }

    function testGetPager()
    {
        $this->assertEquals($this->pagerFantaMock, $this->pfw->getPager());
    }

    function testPageGetters()
    {
        $this->pagerFantaMock
            ->expects($this->once())
            ->method('getNbResults')
        ;
        $this->pfw->getTotal();


        $this->pagerFantaMock
            ->expects($this->once())
            ->method('getNbPages')
        ;
        $this->pfw->getPageCount();

        $this->pagerFantaMock
            ->expects($this->once())
            ->method('getCurrentPage')
        ;
        $this->pagerFantaMock
            ->expects($this->once())
            ->method('getMaxPerPage')
        ;
        $this->pfw->getCurrentPage();
    }

    function testHasNextPrev()
    {
        $this->pagerFantaMock
                ->expects($this->once())
                ->method('hasPreviousPage');
        $this->pfw->hasPreviousPage();

        $this->pagerFantaMock
                ->expects($this->once())
                ->method('hasNextPage');
        $this->pfw->hasNextPage();
    }


    function testGetPrevNextPage() {
        $this->pagerFantaMock
                ->method('getPreviousPage')
                ->willReturn(10);
        $this->pagerFantaMock
                ->method('getNextPage')
                ->willReturn(20);
        $this->pagerFantaMock
                ->method('getMaxPerPage')
                ->will($this->onConsecutiveCalls(123, 456));

        $this->cr
            ->expects($this->exactly(2))
            ->method('generate')
            ->withConsecutive(
                array(array('limit' => 123, 'page' => 10)),
                array(array('limit' => 456, 'page' => 20))
            )
        ;
        $this->pfw->getPreviousPage();
        $this->pfw->getNextPage();
    }
}
