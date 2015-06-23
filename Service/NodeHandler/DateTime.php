<?php

namespace Noxlogic\SerializerBundle\Service\NodeHandler;

use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;

class DateTime implements NodeHandler
{
    public function handle($element, Serializer $serializer, SerializerContext $context)
    {
        if (!$element instanceof \DateTimeInterface) {
            return;
        }

        return $element->format(\DateTime::ISO8601);
    }
}
