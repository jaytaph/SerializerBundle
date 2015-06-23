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
        $output = $data->compile();

        $response = new JsonResponse($output);
        $response->headers->set('Content-Type', 'application/hal+json');

        return $response;
    }
}
