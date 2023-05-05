<?php

namespace Apto\Plugins\RequestForm\Infrastructure\RequestFormBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\RequestForm\Infrastructure\RequestFormBundle\DependencyInjection\RequestFormExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RequestFormBundle extends AbstractAptoBundle
{
    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $this->addDoctrineOrmMapping(
            $container,
            __DIR__ . '/Resources/doctrine',
            'Apto\Plugins\RequestForm\Domain\Core\Model'
        );
    }

    /**
     * @return Extension
     */
    public function getExtension(): Extension
    {
        return new RequestFormExtension();
    }
}