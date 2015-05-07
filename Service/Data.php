<?php

namespace Noxlogic\SerializerBundle\Service;

class Data
{
    protected $state = array();
    protected $links = array();
    protected $embedded = array();
    // protected $curries = array();  // @TODO: not supported

    public static function create()
    {
        return new self();
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function addState($name, $value)
    {
        $this->state[$name] = $value;

        return $this;
    }

    /**
     * @param Data $data
     *
     * @return $this
     */
    public function addEmbedded($name, Data $data)
    {
        $this->embedded[$name] = $data;

        return $this;
    }

    /**
     * @param string $name
     * @param string $href
     * @param array  $attribs
     *
     * @return $this
     */
    public function addLink($name, $href, array $attribs = array())
    {
        $link = array();
        $link['href'] = $href;
        foreach ($attribs as $k => $v) {
            $link[$k] = $v;
        }
        $this->links[$name] = $link;

        return $this;
    }

    /**
     * @return array
     */
    public function compile()
    {
        $output = $this->state;

        $output['_links'] = $this->links;

        foreach ($this->embedded as $name => $resource) {
            $output['_embedded'][$name] = $resource->compile();
        }

        return $output;
    }
}
