<?php

namespace Noxlogic\SerializerBundle\Service;

use Symfony\Component\Routing\RouterInterface;

interface SerializerMapping
{
    /**
     * @param SerializerContext $context
     * @param RouterInterface   $router
     * @param $entity
     *
     * @return Data
     */
    public function entity(SerializerContext $context, RouterInterface $router, $entity, Serializer $serializer);

    /**
     * @param SerializerContext $context
     * @param RouterInterface   $router
     * @param $entity
     *
     * @return mixed
     */
    public function collection(SerializerContext $context, RouterInterface $router, $entity, Serializer $serializer);
}
