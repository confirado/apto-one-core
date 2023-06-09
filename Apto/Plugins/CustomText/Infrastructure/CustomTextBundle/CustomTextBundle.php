<?php

namespace Apto\Plugins\CustomText\Infrastructure\CustomTextBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\CustomText\Infrastructure\CustomTextBundle\DependencyInjection\CustomTextExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CustomTextBundle extends AbstractAptoBundle
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
        return new CustomTextExtension();
    }
}