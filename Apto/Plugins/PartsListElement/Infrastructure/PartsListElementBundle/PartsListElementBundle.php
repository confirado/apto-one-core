<?php

namespace Apto\Plugins\PartsListElement\Infrastructure\PartsListElementBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\PartsListElement\Infrastructure\PartsListElementBundle\DependencyInjection\PartsListElementExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PartsListElementBundle extends AbstractAptoBundle
{
    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    /**
     * @return Extension
     */
    public function getExtension(): Extension
    {
        return new PartsListElementExtension();
    }
}