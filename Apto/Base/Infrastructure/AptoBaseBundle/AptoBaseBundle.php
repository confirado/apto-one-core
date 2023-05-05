<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\AptoBaseExtension;
use Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler\BackendTemplateCompiler;
use Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler\ContentSnippetRegistryCompiler;
use Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler\EventPublisherCompiler;
use Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler\MessageAutoLoadCompiler;
use Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler\TranslationRegistryCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AptoBaseBundle extends AbstractAptoBundle
{
    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $this->addDoctrineOrmMapping($container, __DIR__ . '/Resources/doctrine/money/orm', 'Money');
        $this->addDoctrineOrmMapping($container, __DIR__ . '/Resources/doctrine/core/orm', 'Apto\Base\Domain\Core\Model');
        $this->addDoctrineOrmMapping($container, __DIR__ . '/Resources/doctrine/backend/orm', 'Apto\Base\Domain\Backend\Model');
        $container->addCompilerPass(new MessageAutoLoadCompiler());
        $container->addCompilerPass(new BackendTemplateCompiler());
        $container->addCompilerPass(new EventPublisherCompiler());
        $container->addCompilerPass(new TranslationRegistryCompiler());
        $container->addCompilerPass(new ContentSnippetRegistryCompiler());
    }

    /**
     * @return Extension
     */
    public function getExtension(): Extension
    {
        return new AptoBaseExtension();
    }
}