<?php

namespace Apto\Plugins\SelectBoxElement\Infrastructure\SelectBoxElementBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\SelectBoxElement\Infrastructure\SelectBoxElementBundle\DependencyInjection\SelectBoxElementExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SelectBoxElementBundle extends AbstractAptoBundle
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
            'Apto\Plugins\SelectBoxElement\Domain\Core\Model'
        );
    }

    /**
     * @return Extension
     */
    public function getExtension(): Extension
    {
        return new SelectBoxElementExtension();
    }
}