<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataRegistryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('app.evedata.registry')) {
            return;
        }

        $definition = $container->getDefinition('app.evedata.registry');
        $subscribers = $container->findTaggedServiceIds('eve.manager');

        foreach ($subscribers as $id => $attr) {
            $definition->addMethodCall('set', [new Reference($id)]);
        }
    }
}
