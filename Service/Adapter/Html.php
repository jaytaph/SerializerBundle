<?php

namespace Noxlogic\SerializerBundle\Service\Adapter;

use Noxlogic\SerializerBundle\Service\Data;
use Symfony\Component\HttpFoundation\Response;

class Html implements AdapterInterface
{
    public function getName()
    {
        return 'html';
    }

    public function isSupported($format)
    {
        return ($format == 'html');
    }

    public function convert(Data $data)
    {
        $data = $data->compile();

        return new Response(print_r($data, true), Response::HTTP_OK, array('Content-Type' => 'text/html'));
    }
}
