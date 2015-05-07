<?php

namespace Noxlogic\SerializerBundle\Service\Adapter;

use Noxlogic\SerializerBundle\Service\Data;
use Symfony\Component\HttpFoundation\Response;

interface AdapterInterface
{
    /**
     * Name of the adapter.
     */
    public function getName();

    /**
     * Is the current format supported by this adapter?
     *
     * @param $format
     *
     * @return bool
     */
    public function isSupported($format);

    /**
     * Convert array into response.
     *
     * @param Data $data
     *
     * @return Response
     */
    public function convert(Data $data);
}
