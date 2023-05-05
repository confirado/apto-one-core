<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ContentSnippetRegistryCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Base\Application\Core\Service\ContentSnippet\ContentSnippetRegistry')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Base\Application\Core\Service\ContentSnippet\ContentSnippetRegistry');

        $taggedContentSnippetProvider = $container->findTaggedServiceIds('register_content_snippet_provider');

        foreach ($taggedContentSnippetProvider as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addContentSnippetProvider', [
                    new Reference($id)
                ]
            );
        }
    }
}
