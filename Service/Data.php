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
        if ($value instanceof Data) {
            $value = $value->compile();
        }

        $this->state[$name] = $value;

        return $this;
    }

    /**
     * @param Data $data
     *
     * @return $this
     */
    public function addEmbedded($name, Data $data, $force_array = false)
    {
        if ($data instanceOf DataCollection) {
            foreach ($data->getElements() as $element) {
                if ($force_array) {
                    $element = array($element);
                }
                $this->addEmbedded($name, $element);
            }
            return $this;
        }

        if ($force_array) {
            $data = array($data);
        }

        if (! isset($this->embedded[$name])) {
            $this->embedded[$name] = $data;
            return $this;
        }

        // Already exists, convert to array
        if (! is_array($this->embedded[$name])) {
            $this->embedded[$name] = array($this->embedded[$name]);
        }

        $this->embedded[$name] = array_merge($this->embedded[$name], is_array($data) ? $data : array($data));

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
            if (is_array($resource)) {
                foreach ($resource as $element) {
                    $output['_embedded'][$name][] = $element->compile();
                }
            } else {
                $output['_embedded'][$name] = $resource->compile();
            }
        }

        return $output;
    }
}
