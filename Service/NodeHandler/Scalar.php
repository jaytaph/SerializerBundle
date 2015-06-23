<?php

namespace Noxlogic\SerializerBundle\Service\NodeHandler;

use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;

class Scalar implements NodeHandler
{

    function handle($element, Serializer $serializer, SerializerContext $context)
    {
        if (is_scalar($element)) {
            return $element;
        }

        return null;
    }

}
