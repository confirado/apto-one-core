<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BackendTemplateCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Base\Infrastructure\AptoBaseBundle\Template\TemplateLoader')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Base\Infrastructure\AptoBaseBundle\Template\TemplateLoader');

        $taggedServices = $container->findTaggedServiceIds('register_backend_template');

        foreach ($taggedServices as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addBackendTemplate', [
                    new Reference($id)
                ]
            );
        }
    }
}