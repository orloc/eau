<?php

namespace EveBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use EveBundle\DependencyInjection\Compiler\RepositoryRegistryCompilerPass;

class EveBundle extends Bundle
{

    public function build(ContainerBuilder $container){
        parent::build($container);

        $container->addCompilerPass(new RepositoryRegistryCompilerPass());
    }
}
