<?php

namespace IS\CIValidatorsBundle;

use IS\CIValidatorsBundle\DependencyInjection\Compiler\CIValidatorsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ISCIValidatorsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CIValidatorsCompilerPass());
    }
}
