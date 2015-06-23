<?php

namespace Noxlogic\SerializerBundle\Tests\Service\NodeHandler\Fixtures\Mapping;

use Noxlogic\SerializerBundle\Service\Data;
use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Noxlogic\SerializerBundle\Service\SerializerMapping;
use Noxlogic\SerializerBundle\Tests\Service\NodeHandler\Fixtures\Entity\User;

class UserMapping implements SerializerMapping {


    public function mapping($entity, Serializer $serializer, SerializerContext $context)
    {
        /* @var User $entity */
        return Data::create()
            ->addState('firstname', $entity->getFirstName())
            ->addState('lastname', $entity->getLastName())
            ->addState('fullname', $entity->getFirstName().' '.$entity->getLastName())
            ->addLink('self', 'http://www.google.com')
        ;
    }

}
