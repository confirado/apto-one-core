<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\AptoCatalogExtension;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler\BasketItemDataRegistryCompiler;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler\ElementDefinitionRegistryCompiler;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler\PriceCalculatorRegistryCompiler;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler\PriceRegistryCompiler;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler\ProductElementCopyRegisterCompiler;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler\ProductPluginCopyRegistryCompiler;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection\Compiler\RenderImageRegistryCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AptoCatalogBundle extends AbstractAptoBundle
{
    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $this->addDoctrineOrmMapping($container, __DIR__ . '/Resources/doctrine', 'Apto\Catalog\Domain\Core\Model');
        $container->addCompilerPass(new PriceCalculatorRegistryCompiler());
        $container->addCompilerPass(new PriceRegistryCompiler());
        $container->addCompilerPass(new ProductElementCopyRegisterCompiler());
        $container->addCompilerPass(new RenderImageRegistryCompiler());
        $container->addCompilerPass(new ElementDefinitionRegistryCompiler());
        $container->addCompilerPass(new ProductPluginCopyRegistryCompiler());
        $container->addCompilerPass(new BasketItemDataRegistryCompiler());
    }

    /**
     * @return Extension
     */
    public function getExtension(): Extension
    {
        return new AptoCatalogExtension();
    }
}