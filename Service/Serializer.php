<?php

namespace Noxlogic\SerializerBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Noxlogic\SerializerBundle\Service\NodeHandler as NodeHandlers;
use Noxlogic\SerializerBundle\Service\NodeHandler\NodeHandler;
use Noxlogic\SerializerBundle\Service\OutputAdapter\OutputAdapterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

class Serializer
{
    /**
     * Loaded output adapters.
     *
     * @var array
     */
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
     * @var nodeHandler[]
     */
    protected $nodeHandlers;

    /**
     * @param Registry           $registry
     * @param RouterInterface    $router
     * @param ContainerInterface $container
     */
    public function __construct(Registry $registry, RouterInterface $router, ContainerInterface $container)
    {
        $this->em = $registry->getManager();
        $this->router = $router;
        $this->container = $container;

        $this->addNodeHandler(new NodeHandlers\DoctrineEntity(), 255);
        $this->addNodeHandler(new NodeHandlers\PagerFantaWrapper(), 0);
        $this->addNodeHandler(new NodeHandlers\Collection(), 0);
        $this->addNodeHandler(new NodeHandlers\DateTime(), 0);
        $this->addNodeHandler(new NodeHandlers\Scalar(), -255);
    }

    /**
     * Adds an additional node handler on the given priority (higher the earlier matching).
     *
     * @param NodeHandler $handler
     * @param int         $priority
     */
    public function addNodeHandler(NodeHandler $handler, $priority = 0)
    {
        $priority = (int) $priority;

        if ($priority < -255 || $priority > 255) {
            throw new \LogicException('Priority should be between -255 and 255');
        }

        if (!isset($this->nodeHandlers[$priority])) {
            $this->nodeHandlers[$priority] = array();
        }

        $this->nodeHandlers[$priority][] = $handler;

        krsort($this->nodeHandlers);
    }

    /**
     * Adds an output adapter to the serializer.
     *
     * @param OutputAdapterInterface $adapter
     */
    public function addOutputAdapter(OutputAdapterInterface $adapter)
    {
        $this->outputAdapters[$adapter->getName()] = $adapter;
    }

    /**
     * Removes an output adapter.
     *
     * @param string $name
     */
    public function removeOutputAdapter($name)
    {
        if (!isset($this->outputAdapters[$name])) {
            throw new \InvalidArgumentException("Output adapter '$name' not loaded");
        }

        unset($this->outputAdapters[$name]);
    }

    /**
     * Removes all output adapters.
     */
    public function clearOutputAdapters()
    {
        $this->outputAdapters = array();
    }

    /**
     * Returns an output adapter or throws an exception when not found.
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
     * Checks if an output adapter is loaded.
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
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Returns serialized data for given element.
     *
     * @param mixed             $element
     * @param SerializerContext $context
     *
     * @return Data
     */
    public function serialize($element, SerializerContext $context = null)
    {
        if ($context == null) {
            $context = SerializerContext::create();
        }

        $data = null;

        // Iterate each handler in priority until one can handle it
        foreach ($this->nodeHandlers as $priority => $handlers) {
            foreach ($handlers as $handler) {
                $data = $handler->handle($element, $this, $context);

                if ($data !== null) {
                    break 2;
                }
            }
        }

        if ($data == null) {
            throw new \LogicException('Cannot handle unknown element type');
        }

        return $data;
    }

    /**
     * Returns a response created by the adapter for given format.
     *
     * @param Data $data
     * @param $format
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createResponse(Data $data, $format)
    {
        $outputAdapter = $this->getOutputAdapter($format);

        return $outputAdapter->convert($data);
    }
}
