<?php

namespace Noxlogic\SerializerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MappingCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('noxlogic_serializer')) {
            return;
        }

        $definition = $container->findDefinition('noxlogic_serializer');

        $taggedServices = $container->findTaggedServiceIds('noxlogic.serializer.mapping');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addServiceMapping', array(new Reference($id)));
        }
    }
}
