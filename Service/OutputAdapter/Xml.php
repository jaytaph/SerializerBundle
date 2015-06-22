<?php

namespace Noxlogic\SerializerBundle\Service\OutputAdapter;

use Noxlogic\SerializerBundle\Service\Data;
use Symfony\Component\HttpFoundation\Response;

class Xml implements OutputAdapterInterface
{
    public function getName()
    {
        return 'xml';
    }

    public function isSupported($format)
    {
        return ($format == 'xml');
    }

    public function convert(Data $data)
    {
        $data = $data->compile();

        return new Response('XML: '.print_r($data, true), Response::HTTP_OK, array('Content-Type' => 'text/xml'));
    }
}
