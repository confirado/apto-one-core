<?php

namespace Apto\Plugins\WidthHeightElement\Infrastructure\WidthHeightElementBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\WidthHeightElement\Infrastructure\WidthHeightElementBundle\DependencyInjection\WidthHeightElementExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WidthHeightElementBundle extends AbstractAptoBundle
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
        return new WidthHeightElementExtension();
    }
}