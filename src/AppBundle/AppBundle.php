<?php

namespace AppBundle;

use AppBundle\DependencyInjection\Compiler\DataRegistryCompilerPass;
use AppBundle\DependencyInjection\Compiler\DispatcherCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{

    public function getParent(){
        return 'FOSUserBundle';
    }

    public function build(ContainerBuilder $container){
        parent::build($container);

        $container->addCompilerPass(new DispatcherCompilerPass());
        $container->addCompilerPass(new DataRegistryCompilerPass());
    }
}
