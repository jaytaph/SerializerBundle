<?php

namespace Noxlogic\SerializerBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Noxlogic\SerializerBundle\Service\Adapter\AdapterInterface;
use Noxlogic\SerializerBundle\Service\Collection\PagerFantaWrapper;
use Symfony\Component\Routing\RouterInterface;

class Serializer
{
    protected $adapters = array();

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected $em;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $serviceMappings = array();


    public function __construct(Registry $registry, RouterInterface $router)
    {
        $this->em = $registry->getManager();
        $this->router = $router;
    }

    /**
     * Adds a mapping to the service maps.
     * @param $className
     * @param $mapping
     */
    function addServiceMapping($mapping) {
        if (! $mapping instanceof ServiceableSerializerMapping) {
            throw new InvalidArgumentException('Mapping '.get_class($mapping).' must implement ServiceableSerializerMapping');
        }

        $this->serviceMappings[$mapping->getEntityClassName()] = $mapping;
    }

    /**
     * @param AdapterInterface $adapter
     */
    public function addAdapter(AdapterInterface $adapter)
    {
        $this->adapters[$adapter->getName()] = $adapter;
    }

    /**
     * @param string $name
     */
    public function removeAdapter($name)
    {
        if (!isset($this->adapters[$name])) {
            throw new \InvalidArgumentException("Adapter '$name' not loaded");
        }
        unset($this->adapters[$name]);
    }

    /**
     *
     */
    public function clearAdapters()
    {
        $this->adapters = array();
    }

    /**
     * @param string $name
     *
     * @return AdapterInterface
     */
    public function getAdapter($name)
    {
        if (!isset($this->adapters[$name])) {
            throw new \InvalidArgumentException("Adapter '$name' not loaded");
        }

        return $this->adapters[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isAdapterLoaded($name)
    {
        return (isset($this->adapters[$name]));
    }

    /**
     * @param $data
     * @param SerializerContext $context
     *
     * @return DataCollection
     */
    public function serializeCollection(PagerFantaWrapper $wrapper, SerializerContext $context)
    {
        $collection = DataCollection::create();

        $collection->addLink('self', $wrapper->getCurrentPage());

        if ($wrapper->hasPreviousPage()) {
            $collection->addLink('prev', $wrapper->getPreviousPage());
        }
        if ($wrapper->hasNextPage()) {
            $collection->addLink('next', $wrapper->getNextPage());
        }
        $collection->addState('count', $wrapper->getTotal());
        $collection->addState('pages', $wrapper->getPageCount());

        foreach ($wrapper->getPager()->getCurrentPageResults() as $item) {
            // We are inside a collection. We might want a different layout.
            $inCollection = $context->isInCollection();
            $context->setInCollection(true);

            $element = $this->_serialize($item, $context, $this);
            $collection->addElement($element);

            $context->setInCollection($inCollection);
        }

        return $collection;
    }

    public function serializeArray(array $elements, SerializerContext $context)
    {
        $collection = DataCollection::create();

        $collection->addState('count', count($elements));
        foreach ($elements as $item) {
            // Don't treat array elements as collections
            $inCollection = $context->isInCollection();
            $context->setInCollection(false);

            $element = $this->_serialize($item, $context, $this);
            $collection->addElement($element);

            $context->setInCollection($inCollection);
        }

        return $collection;
    }

    /**
     * @param $element
     * @param SerializerContext $context
     *
     * @return Data
     */
    protected function serializeElement($element, SerializerContext $context)
    {
        // Might be a doctrine proxy class. Make sure we get the actual class
        $className = \Doctrine\Common\Util\ClassUtils::getClass($element);

        // Check if we need to load the mapping through a service. This is useful when the mapping needs dependencies like doctrine or others.

        // Mapping already exists
        if (isset($this->serviceMappings[$className])) {
            $mapping = $this->serviceMappings[$className];

        } else {
            // If it's an entity, convert into a mapping class name
            if (strpos($className, '\\Entity\\') !== false) {
                $className = str_replace('\\Entity\\', '\\Mapping\\', $className) . 'Mapping';
            }

            // Check if it exists
            if (!class_exists($className)) {
                throw new \InvalidArgumentException("Mapping $className does not exist");
            }

            // Check if the mapping class implements our needed interface
            $mapping = new $className();
        }


        if (!$mapping instanceof SerializerMapping) {
            throw new \InvalidArgumentException("Mapping class $className must implement the SerializerMapping interface");
        }

        if ($context->isInCollection()) {
            $data = $mapping->collection($context, $this->router, $element, $this);
        } else {
            $data = $mapping->entity($context, $this->router, $element, $this);
        }

        return $data;
    }

    /**
     * @param $element
     * @param SerializerContext $context
     *
     * @return Data|DataCollection
     */
    protected function _serialize($element, SerializerContext $context)
    {
        if (is_array($element)) {
            return $this->serializeArray($element, $context);
        }

        if ($element instanceof PagerFantaWrapper) {
            return $this->serializeCollection($element, $context);
        }

        return $this->serializeElement($element, $context);
    }

    /**
     * Returns serialized data for given element
     *
     * @param mixed             $element
     * @param SerializerContext $context
     *
     * @return Data|DataCollection
     */
    public function serialize($element, SerializerContext $context)
    {
        return $this->_serialize($element, $context);
    }

    /**
     * Returns a response created by the adapter for given format.
     *
     * @param Data $data
     * @param $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createResponse(Data $data, $format)
    {
        $adapter = $this->getAdapter($format);

        return $adapter->convert($data);
    }
}
