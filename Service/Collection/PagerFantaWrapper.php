<?php

namespace Noxlogic\SerializerBundle\Service\Collection;

use Pagerfanta\Pagerfanta;

/**
 * @TODO: Make a nice wrapper/adapter system around other pagination systems
 */
class PagerFantaWrapper
{
    /**
     * @var Pagerfanta
     */
    protected $pager;
    protected $routing;
    protected $pageName;
    protected $limitName;

    public function __construct(Pagerfanta $pager, CollectionRouting $routing, $pageName = 'page', $limitName = 'limit', $elementName = "elements")
    {
        $this->pager = $pager;
        $this->routing = $routing;
        $this->pageName = $pageName;
        $this->limitName = $limitName;
        $this->elementName = $elementName;
    }

    /**
     * @return mixed
     */
    public function getPager()
    {
        return $this->pager;
    }

    public function getTotal()
    {
        return $this->pager->getNbResults();
    }

    public function getPageCount()
    {
        return $this->pager->getNbPages();
    }

    public function getCurrentPage()
    {
        return $this->routing->generate(array(
            $this->pageName => $this->pager->getCurrentPage(),
            $this->limitName => $this->pager->getMaxPerPage(),
        ));
    }

    public function hasPreviousPage()
    {
        return $this->pager->hasPreviousPage();
    }

    public function hasNextPage()
    {
        return $this->pager->hasNextPage();
    }

    public function getPreviousPage()
    {
        return $this->routing->generate(array(
            $this->pageName => $this->pager->getPreviousPage(),
            $this->limitName => $this->pager->getMaxPerPage(),
        ));
    }

    public function getNextPage()
    {
        return $this->routing->generate(array(
            $this->pageName => $this->pager->getNextPage(),
            $this->limitName => $this->pager->getMaxPerPage(),
        ));
    }

    /**
     * @return string
     */
    public function getElementName()
    {
        return $this->elementName;
    }

}
