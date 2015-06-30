<?php

namespace Noxlogic\SerializerBundle\Service\NodeHandler;

use Noxlogic\SerializerBundle\Service\Collection\PagerFantaWrapper as PagerFantaWrapperCollection;
use Noxlogic\SerializerBundle\Service\Data;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;

class PagerFantaWrapper implements NodeHandler
{
    /**
     * @param $element
     * @param Serializer        $serializer
     * @param SerializerContext $context
     *
     * @return Data|null
     */
    public function handle($element, Serializer $serializer, SerializerContext $context)
    {
        if (!$element instanceof PagerFantaWrapperCollection) {
            return;
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

        $embeds = array();
        $states = array();
        foreach ($wrapper->getPager()->getCurrentPageResults() as $element) {
            $embeddedData = $serializer->serialize($element, $context);

            if ($embeddedData instanceOf Data) {
                $embeds[] = $embeddedData;
            } else {
                $states[] = $embeddedData;
            }
        }

        // We add them at the end to make sure we are always an array, even when we only have one element.
        if (count($embeds)) {
            $data->addEmbedded($wrapper->getElementName(), $embeds);
        }
        if (count($states)) {
            $data->addState($wrapper->getElementName(), $states);
        }


        return $data;
    }
}
