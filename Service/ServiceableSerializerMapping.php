<?php

namespace Noxlogic\SerializerBundle\Service;

use Symfony\Component\Routing\RouterInterface;

interface ServiceableSerializerMapping extends SerializerMapping
{

    public function getEntityClassName();

}
