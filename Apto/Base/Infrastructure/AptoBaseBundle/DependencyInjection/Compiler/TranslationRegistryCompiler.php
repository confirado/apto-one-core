<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TranslationRegistryCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Base\Application\Core\Service\Translation\TranslationRegistry')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Base\Application\Core\Service\Translation\TranslationRegistry');

        $taggedTranslationExportProvider = $container->findTaggedServiceIds('register_translation_export_provider');
        $taggedTranslationImportProvider = $container->findTaggedServiceIds('register_translation_import_provider');

        foreach ($taggedTranslationExportProvider as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addTranslationExportProvider', [
                    new Reference($id)
                ]
            );
        }

        foreach ($taggedTranslationImportProvider as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addTranslationImportProvider', [
                    new Reference($id)
                ]
            );
        }
    }
}