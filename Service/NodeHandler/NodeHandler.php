<?php

namespace Noxlogic\SerializerBundle\Service\NodeHandler;

use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;

interface NodeHandler
{
    public function handle($element, Serializer $serializer, SerializerContext $context);
}
