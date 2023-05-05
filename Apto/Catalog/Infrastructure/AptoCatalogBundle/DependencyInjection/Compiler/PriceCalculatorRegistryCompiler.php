<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PriceCalculatorRegistryCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculatorRegistry')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculatorRegistry');

        $taggedPriceCalculators = $container->findTaggedServiceIds('register_price_calculator');
        $taggedPriceProviders = $container->findTaggedServiceIds('register_price_provider');
        $taggedProductPriceProviders = $container->findTaggedServiceIds('register_product_price_provider');
        $taggedProductSurchargeProviders = $container->findTaggedServiceIds('register_product_surcharge_provider');


        foreach ($taggedPriceCalculators as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addPriceCalculator', [
                    new Reference($id)
                ]
            );
        }

        foreach ($taggedPriceProviders as $id => $tags) {
            foreach ($tags as $index => $tag) {
                // add the subscriber to the generic publisher service
                $definition->addMethodCall('addPriceProvider', [
                        new Reference($id)
                    ]
                );
            }

        }

        // add product price providers to registry
        foreach ($taggedProductPriceProviders as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addProductPriceProvider', [
                    new Reference($id)
                ]
            );
        }

        // add product surcharge providers to registry
        foreach ($taggedProductSurchargeProviders as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addProductSurchargeProvider', [
                    new Reference($id)
                ]
            );
        }
    }
}