<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RenderImageRegistryCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Catalog\Application\Core\Service\RenderImage\RenderImageRegistry')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Catalog\Application\Core\Service\RenderImage\RenderImageRegistry');

        $taggedRenderImageProvider = $container->findTaggedServiceIds('register_render_image_provider');

        foreach ($taggedRenderImageProvider as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addRenderImageProvider', [
                    new Reference($id)
                ]
            );
        }

        $taggedRenderImageReducer = $container->findTaggedServiceIds('register_render_image_reducer');

        foreach ($taggedRenderImageReducer as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addRenderImageReducer', [
                    new Reference($id)
                ]
            );
        }
    }
}