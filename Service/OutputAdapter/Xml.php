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
        $output = $data->compile();

        return new Response('XML: '.print_r($output, true), 200, array('Content-Type' => 'text/xml'));
    }
}
