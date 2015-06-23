<?php

namespace Noxlogic\SerializerBundle\Service;

interface SerializerMapping
{
    /**
     * @param $entity
     * @param Serializer        $serializer
     * @param SerializerContext $context
     *
     * @return Data|null
     */
    public function mapping($entity, Serializer $serializer, SerializerContext $context);
}
