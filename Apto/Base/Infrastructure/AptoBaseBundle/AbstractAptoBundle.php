<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

abstract class AbstractAptoBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     * @param string $xmlPath
     * @param string $namespacePrefix
     */
    protected function addDoctrineOrmMapping(ContainerBuilder $container, string $xmlPath, string $namespacePrefix)
    {
        $container->addCompilerPass(
            $this->buildOrmMappingCompilerPass($xmlPath, $namespacePrefix)
        );
    }

    /**
     * @param string $xmlPath
     * @param string $namespacePrefix
     * @return DoctrineOrmMappingsPass
     */
    protected function buildOrmMappingCompilerPass(string $xmlPath, string $namespacePrefix): DoctrineOrmMappingsPass
    {
        return DoctrineOrmMappingsPass::createXmlMappingDriver(
            [$xmlPath => $namespacePrefix]
        );
    }
}