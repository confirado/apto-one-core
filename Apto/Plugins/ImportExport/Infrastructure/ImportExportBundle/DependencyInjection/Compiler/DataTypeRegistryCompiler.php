<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataTypeRegistryCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataTypeRegistry')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataTypeRegistry');
        $taggedServices = $container->findTaggedServiceIds('register_import_export_data_type');

        foreach ($taggedServices as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addDataType', [
                    new Reference($id)
                ]
            );
        }
    }
}