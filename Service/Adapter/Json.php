<?php

namespace Noxlogic\SerializerBundle\Service\Adapter;

use Noxlogic\SerializerBundle\Service\Data;
use Symfony\Component\HttpFoundation\JsonResponse;

class Json implements AdapterInterface
{
    public function getName()
    {
        return 'json';
    }

    public function isSupported($format)
    {
        return ($format == 'json');
    }

    public function convert(Data $data)
    {
        $data = $data->compile();

        return new JsonResponse($data);
    }
}
