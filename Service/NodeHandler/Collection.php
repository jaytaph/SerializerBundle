<?php

namespace Noxlogic\SerializerBundle\Service\NodeHandler;

use Noxlogic\SerializerBundle\Service\Data;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Collection implements NodeHandler
{
    /**
     * @param $element
     * @param SerializerContext $context
     * @return null
     */
    function handle($element, Serializer $serializer, SerializerContext $context)
    {
        if (! $element instanceof \Traversable && ! is_array($element)) {
            return null;
        }

        // Make sure that we always work with traversable objects instead of arrays
        if (is_array($element)) {
            $element = new \ArrayIterator($element);
        }

        $collection = $element;

        $elements = array();
        foreach ($collection as $element) {
            $elements[] = $serializer->serialize($element, $context);
        }

        $data = Data::create()
            ->alwaysDisplayLinks(false)
            ->addState('count', count($elements))
            ->addState('elements', $elements)
        ;

        return $data;
    }

}
