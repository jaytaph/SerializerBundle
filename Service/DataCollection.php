<?php

namespace Noxlogic\SerializerBundle\Service;

class DataCollection extends Data
{
    protected $elements = array();

    /**
     * @return DataCollection
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param Data $data
     *
     * @return $this
     */
    public function addElement(Data $data)
    {
        $this->elements[] = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function compile()
    {
        $output = parent::compile();

        foreach ($this->elements as $element) {
            $output['_elements'][] = $element->compile();
        }

        return $output;
    }
}
