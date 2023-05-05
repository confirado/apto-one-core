<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProductElementCopyRegisterCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('Apto\Catalog\Application\Backend\Service\Product\ProductElementCopyRegistry')) {
            return;
        }

        $definition = $container->findDefinition(
            'Apto\Catalog\Application\Backend\Service\Product\ProductElementCopyRegistry'
        );

        $taggedProviders = $container->findTaggedServiceIds('register_product_element_copy_provider');

        foreach ($taggedProviders as $id => $taggedProvider) {
            $definition->addMethodCall(
                'addProductElementCopyProvider',
                [
                    new Reference($id)
                ]
            );
        }
    }
}
