<?php

namespace Noxlogic\SerializerBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\Collection;
use Noxlogic\SerializerBundle\Service\Collection\PagerFantaWrapper;
use Noxlogic\SerializerBundle\Service\OutputAdapter\OutputAdapterInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

class Serializer
{
    protected $outputAdapters = array();

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected $em;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var ContainerInterface
     */
    protected $container;


    /**
     * @param Registry $registry
     * @param RouterInterface $router
     * @param ContainerInterface $container
     */
    public function __construct(Registry $registry, RouterInterface $router, ContainerInterface $container)
    {
        $this->em = $registry->getManager();
        $this->router = $router;
        $this->container = $container;
    }

    /**
     * Adds an output adapter to the serializer
     *
     * @param OutputAdapterInterface $adapter
     */
    public function addOutputAdapter(OutputAdapterInterface $adapter)
    {
        $this->outputAdapters[$adapter->getName()] = $adapter;
    }

    /**
     * Removes an output adapter
     *
     * @param string $name
     */
    public function removeOutputAdapter($name)
    {
        if (!isset($this->outputAdapters[$name])) {
            throw new \InvalidArgumentException("Adapter '$name' not loaded");
        }
        unset($this->outputAdapters[$name]);
    }

    /**
     * Removes all output adapters
     */
    public function clearOutputAdapters()
    {
        $this->outputAdapters = array();
    }

    /**
     * Returns an output adapter or throws an exception when not found
     *
     * @param string $name
     *
     * @return OutputAdapterInterface
     */
    public function getOutputAdapter($name)
    {
        if (!isset($this->outputAdapters[$name])) {
            throw new \InvalidArgumentException("Output adapter '$name' not loaded");
        }

        return $this->outputAdapters[$name];
    }

    /**
     * Checks if an output adapter is loaded
     *
     * @param string $name
     *
     * @return bool
     */
    public function isOutputAdapterLoaded($name)
    {
        return (isset($this->outputAdapters[$name]));
    }

    /**
     * @param $data
     * @param SerializerContext $context
     *
     * @return DataCollection
     */
    public function serializeCollection($name, PagerFantaWrapper $wrapper, SerializerContext $context)
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


        // We are inside a collection. We might want a different layout.
        $inCollection = $context->isInCollection();
        $context->setInCollection(true);

        foreach ($wrapper->getPager()->getCurrentPageResults() as $item) {
            $element = $this->_serialize($item, $context, $this);
            $collection->addEmbedded($name, $element);
        }
        $context->setInCollection($inCollection);


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

        if (!$mapping instanceof SerializerMapping) {
            throw new \InvalidArgumentException("Mapping class $className must implement the SerializerMapping interface");
        }

        if ($mapping instanceof ContainerAwareInterface) {
            $mapping->setContainer($this->container);
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

        if ($element instanceof Collection) {
            return $this->serializeArray($element->toArray(), $context);
        }

        if ($element instanceof PagerFantaWrapper) {
            return $this->serializeCollection($element->getElementName(), $element, $context);
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
        $outputAdapter = $this->getOutputAdapter($format);

        return $outputAdapter->convert($data);
    }
}
