<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PriceRegistryCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Catalog\Application\Backend\Service\Price\PriceRegistry')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Catalog\Application\Backend\Service\Price\PriceRegistry');

        $taggedPriceExportProvider = $container->findTaggedServiceIds('register_price_export_provider');
        $taggedPriceImportProvider = $container->findTaggedServiceIds('register_price_import_provider');

        foreach ($taggedPriceExportProvider as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addPriceExportProvider', [
                    new Reference($id)
                ]
            );
        }

        foreach ($taggedPriceImportProvider as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addPriceImportProvider', [
                    new Reference($id)
                ]
            );
        }
    }
}