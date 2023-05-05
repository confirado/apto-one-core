<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BasketItemDataRegistryCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Catalog\Application\Frontend\Service\BasketItemDataRegistry')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Catalog\Application\Frontend\Service\BasketItemDataRegistry');

        $taggedRenderImageProvider = $container->findTaggedServiceIds('register_basket_item_data_provider');
        foreach ($taggedRenderImageProvider as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addBasketItemDataProvider', [
                    new Reference($id)
                ]
            );
        }
    }
}