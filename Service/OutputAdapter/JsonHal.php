<?php

namespace Noxlogic\SerializerBundle\Service\OutputAdapter;

use Noxlogic\SerializerBundle\Service\Data;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonHal implements OutputAdapterInterface
{
    public function getName()
    {
        return 'json-hal';
    }

    public function isSupported($format)
    {
        return ($format == 'json-hal');
    }

    public function convert(Data $data)
    {
        $data = $data->compile();

        return new JsonResponse($data);
    }
}
