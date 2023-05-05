<?php

namespace Apto\Plugins\DateElement\Infrastructure\DateElementBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\DateElement\Infrastructure\DateElementBundle\DependencyInjection\DateElementExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DateElementBundle extends AbstractAptoBundle
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
        return new DateElementExtension();
    }
}