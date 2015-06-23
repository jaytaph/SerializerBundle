<?php

namespace Noxlogic\SerializerBundle\Service;

class Data
{
    /**
     * Array with all properties (state, _links, _embedded etc)
     *
     * @var array
     */
    protected $properties = array();

    /**
     * Always add the `_links`, even when its empty
     *
     * @var bool
     */
    protected $displayLinks = true;

    public static function create()
    {
        return new self();
    }

    /**
     *
     * @param $displayLinks
     * @return $this
     */
    public function alwaysDisplayLinks($displayLinks) {
        $this->displayLinks = $displayLinks;

        return $this;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function addState($name, $value)
    {
        $this->addEntry('state', $name, $value);

        return $this;
    }

    /**
     * @param Data $data
     *
     * @return $this
     */
    public function addEmbedded($name, Data $data)
    {
        $this->addEntry('embedded', $name, $data);

        return $this;
    }

    /**
     * @param string $name
     * @param string $href
     * @param array  $attributes
     *
     * @return $this
     */
    public function addLink($name, $href, array $attributes = array())
    {
        $link = array();
        $link['href'] = $href;
        foreach ($attributes as $k => $v) {
            $link[$k] = $v;
        }

        $this->addEntry('links', $name, $link);

        return $this;
    }


    /**
     * Adds a name/value to a certain property of this class. Will add scalar elements, but when arrays are added, or
     * when multiple value in the same property[name] are stored, it will automatically be converted to an array
     *
     * @param $property
     * @param $value
     */
    protected function addEntry($property, $name, $value)
    {
        // Property does not exist, create it first
        if (! isset($this->properties[$property][$name])) {
            $this->properties[$property][$name] = $value;

            return;
        }

        // Property is not an array, convert it first
        if (! is_array($this->properties[$property][$name])) {
            $this->properties[$property][$name] = array($this->properties[$property][$name]);
        }

        // Wrap value inside an array if needed
        if (! is_array($value)) {
            $value = array($value);
        }

        // Merge the value(s) to the property
        $this->properties[$property][$name] = array_merge($this->properties[$property][$name], $value);
    }


    /**
     * Compile the whole data object (including underlying data objects)
     *
     * @return array
     */
    public function compile()
    {
        // Always display state elements (even when empty)
        $output = $this->compileProperty('state');

        // Display links elements when available or when forced
        $linksOutput = $this->compileProperty('links');
        if ($linksOutput || $this->displayLinks) {
            $output['_links'] = $linksOutput;
        }

        // Display embedded elements when available
        $embeddedOutput = $this->compileProperty('embedded');
        if ($embeddedOutput) {
            $output['_embedded'] = $embeddedOutput;
        }

        return $output;
    }

    /**
     * Compile a property array (embedded, links, state etc) into a completely compiled array
     *
     * @param $property
     * @return array
     */
    function compileProperty($property)
    {
        $output = array();

        if (! isset($this->properties[$property])) {
            return array();
        }

        foreach ($this->properties[$property] as $name => $resource) {
            if (is_array($resource)) {
                foreach ($resource as $key => $element) {
                    $output[$name][$key] = $this->getElementValue($element);
                }
            } else {
                $output[$name] = $this->getElementValue($resource);
            }
        }

        return $output;
    }

    /**
     * Returns either direct element, or when it's a Data object, it's compiled value
     *
     * @param $element
     * @return array
     */
    protected function getElementValue($element)
    {
        if ($element instanceof Data) {
            return $element->compile();
        }

        return $element;
    }

}
