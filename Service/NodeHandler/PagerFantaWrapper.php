<?php

namespace Noxlogic\SerializerBundle\Service\NodeHandler;

use Noxlogic\SerializerBundle\Service\Collection\PagerFantaWrapper as PagerFantaWrapperCollection;
use Noxlogic\SerializerBundle\Service\Data;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PagerFantaWrapper implements NodeHandler
{

    /**
     * @param $element
     * @param Serializer $serializer
     * @param SerializerContext $context
     * @return Data|null
     */
    function handle($element, Serializer $serializer, SerializerContext $context)
    {
        if (! $element instanceof PagerFantaWrapperCollection) {
            return null;
        }

        $wrapper = $element;

        $data = Data::create()
            ->addLink('self', $wrapper->getCurrentPage())
            ->addState('count', $wrapper->getTotal())
            ->addState('pages', $wrapper->getPageCount())
        ;

        if ($wrapper->hasPreviousPage()) {
            $data->addLink('prev', $wrapper->getPreviousPage());
        }
        if ($wrapper->hasNextPage()) {
            $data->addLink('next', $wrapper->getNextPage());
        }

        $elements = array();
        foreach ($wrapper->getPager()->getCurrentPageResults() as $element) {
            $elements[] = $serializer->serialize($element, $context);
        }
        $data->addState($wrapper->getElementName(), $elements);

        return $data;
    }

}
