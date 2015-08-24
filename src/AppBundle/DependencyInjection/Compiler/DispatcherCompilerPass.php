<?php

namespace AppBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DispatcherCompilerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container){

        if (!$container->hasDefinition('app.task.dispatcher')){
            return;
        }

        $definition = $container->getDefinition('app.task.dispatcher');
        $subscribers = $container->findTaggedServiceIds('app.task.subscriber');

        foreach ($subscribers as $id => $attr){
            $definition->addMethodCall('addSubscriber', [new Reference($id)] );
        }
    }
}