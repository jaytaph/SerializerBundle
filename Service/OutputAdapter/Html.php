<?php

namespace Noxlogic\SerializerBundle\Service\OutputAdapter;

use Noxlogic\SerializerBundle\Service\Data;
use Symfony\Component\HttpFoundation\Response;

class Html implements OutputAdapterInterface
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
        $output = $data->compile();

        return new Response(print_r($output, true), 200, array('Content-Type' => 'text/html'));
    }
}
