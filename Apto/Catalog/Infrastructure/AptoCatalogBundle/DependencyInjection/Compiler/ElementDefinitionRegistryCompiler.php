<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ElementDefinitionRegistryCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\ElementDefinitionRegistry')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\ElementDefinitionRegistry');

        $taggedElementDefinitions = $container->findTaggedServiceIds('register_element_definition');
        $taggedStaticValuesProviders = $container->findTaggedServiceIds('register_element_static_values_provider');

        foreach ($taggedElementDefinitions as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('registerElementDefinition', [
                    new Reference($id)
                ]
            );
        }

        foreach ($taggedStaticValuesProviders as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addStaticValuesProvider', [
                    new Reference($id)
                ]
            );
        }
    }
}
