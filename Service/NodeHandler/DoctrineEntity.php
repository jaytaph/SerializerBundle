<?php

namespace Noxlogic\SerializerBundle\Service\NodeHandler;

use Noxlogic\SerializerBundle\Service\Serializer;
use Noxlogic\SerializerBundle\Service\SerializerContext;
use Noxlogic\SerializerBundle\Service\SerializerMapping;

class DoctrineEntity implements NodeHandler
{
    /**
     * @param $element
     * @param SerializerContext $context
     */
    public function handle($element, Serializer $serializer, SerializerContext $context)
    {
        if (!is_object($element)) {
            return;
        }

        // the current element might be a doctrine proxy class. Make sure we get the actual class.
        $className = \Doctrine\Common\Util\ClassUtils::getClass($element);

        $mappingClassName = $className;
        // If it's an entity, convert into a mapping class name
        if (strpos($mappingClassName, '\\Entity\\') !== false) {
            $mappingClassName = str_replace('\\Entity\\', '\\Mapping\\', $mappingClassName);
        }
        $mappingClassName .= 'Mapping';

        // Check if the mapping class exists
        if (!class_exists($mappingClassName)) {
            return;
        }

        // Check if the mapping class implements our needed interface
        $mapping = new $mappingClassName();
        if (!$mapping instanceof SerializerMapping) {
            throw new \InvalidArgumentException("Mapping class $mappingClassName must implement the SerializerMapping interface");
        }

//        if ($context->isInCollection()) {
//            $data = $mapping->collection($context, $this->router, $element, $this);
//        } else {
            $data = $mapping->mapping($element, $serializer, $context);
//        }

        return $data;
    }
}
