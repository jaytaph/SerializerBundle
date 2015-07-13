<?php

namespace Noxlogic\SerializerBundle\Service\NodeHandler;

use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;

class DateTime implements NodeHandler
{
    public function handle($element, Serializer $serializer, SerializerContext $context)
    {
        // PHP 5.4 compatibility as \DateTimeInterface needs PHP 5.5
        if (!interface_exists('\\DateTimeInterface', false) && !$element instanceof \DateTime) {
            /** @codeCoverageIgnoreStart */
            return;
            /** @codeCoverageIgnoreEnd */
        }

        if (interface_exists('\\DateTimeInterface', false) && !$element instanceof \DateTimeInterface) {
            return;
        }

        return $element->format(\DateTime::ISO8601);
    }
}
