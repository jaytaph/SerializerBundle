<?php

namespace Noxlogic\SerializerBundle\Service\NodeHandler;

use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;

class DateTime implements NodeHandler
{

    function handle($element, Serializer $serializer, SerializerContext $context)
    {
        if (! $element instanceof \DateTimeInterface) {
            return null;
        }

        return $element->format(\DateTime::ISO8601);
    }

}
