<?php

namespace Noxlogic\SerializerBundle\Service;

use Symfony\Component\Routing\RouterInterface;

interface SerializerMapping
{

    /**
     * @param $entity
     * @param Serializer $serializer
     * @param SerializerContext $context
     * @return Data|null
     */
    public function mapping($entity, Serializer $serializer, SerializerContext $context);

}
