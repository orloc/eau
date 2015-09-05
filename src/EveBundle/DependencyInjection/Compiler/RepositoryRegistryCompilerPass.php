<?php

namespace EveBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RepositoryRegistryCompilerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container){

        if (!$container->hasDefinition('evedata.registry')){
            return;
        }

        $definition = $container->getDefinition('evedata.registry');
        $subscribers = $container->findTaggedServiceIds('evedata.repository');

        foreach ($subscribers as $id => $attr){
            $definition->addMethodCall('set', [new Reference($id)] );
        }
    }
}