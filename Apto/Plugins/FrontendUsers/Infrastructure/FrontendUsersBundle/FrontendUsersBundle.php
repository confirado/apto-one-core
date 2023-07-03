<?php

namespace Apto\Plugins\FrontendUsers\Infrastructure\FrontendUsersBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\FrontendUsers\Infrastructure\FrontendUsersBundle\DependencyInjection\FrontendUsersExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FrontendUsersBundle extends AbstractAptoBundle
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
        return new FrontendUsersExtension();
    }
}
