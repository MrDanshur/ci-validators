<?php

namespace InternalSite\CoreBundle\DependencyInjection\Compiler;

use IS\CIValidatorsBundle\Validator\ValidatorCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EntityValidatorCompilerPass.
 */
class CIValidatorsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ValidatorCollection::class)) {
            return;
        }

        $factory           = $container->getDefinition(ValidatorCollection::class);
        $entityIdentifiers = $container->findTaggedServiceIds('validator.entity_validator');

        foreach ($entityIdentifiers as $id => $tags) {
            $factory->addMethodCall('addValidator', [new Reference($id)]);
        }
    }
}
