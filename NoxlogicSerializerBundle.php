<?php

namespace Noxlogic\SerializerBundle;

use Noxlogic\SerializerBundle\DependencyInjection\AdapterCompilerPass;
use Noxlogic\SerializerBundle\DependencyInjection\MappingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NoxlogicSerializerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AdapterCompilerPass());
        $container->addCompilerPass(new MappingCompilerPass());
    }
}
