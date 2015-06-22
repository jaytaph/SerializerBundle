<?php

namespace Noxlogic\SerializerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AdapterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('noxlogic_serializer')) {
            return;
        }

        $definition = $container->findDefinition('noxlogic_serializer');

        $taggedServices = $container->findTaggedServiceIds('noxlogic.serializer.output.adapter');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addOutputAdapter', array(new Reference($id)));
        }
    }
}
