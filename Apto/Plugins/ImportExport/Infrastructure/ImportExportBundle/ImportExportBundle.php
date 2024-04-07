<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\DependencyInjection\Compiler\DataTypeRegistryCompiler;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\DependencyInjection\ImportExportExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ImportExportBundle extends AbstractAptoBundle
{
    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DataTypeRegistryCompiler());

    }

    /**
     * @return Extension
     */
    public function getExtension(): Extension
    {
        return new ImportExportExtension();
    }
}