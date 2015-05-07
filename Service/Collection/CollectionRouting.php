<?php

namespace Noxlogic\SerializerBundle\Service\Collection;

use Symfony\Component\Routing\RouterInterface;

class CollectionRouting
{
    /** @var RouterInterface */
    protected $router;

    /** @var string */
    protected $route;

    /** @var array */
    protected $args;

    /**
     * @param $route
     * @param array $args
     */
    public function __construct($route, array $args = array())
    {
        $this->route = $route;
        $this->args = $args;
    }

    /**
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param array $additional_args
     *
     * @return string
     */
    public function generate(array $additional_args = array())
    {
        if (!$this->router) {
            throw new \LogicException('A router must have been set first before calling Routing::generate()');
        }

        return $this->router->generate($this->route, array_merge($this->args, $additional_args));
    }
}
