<?php

namespace Apto\Plugins\PricePerUnitElement\Infrastructure\PricePerUnitElementBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\PricePerUnitElement\Infrastructure\PricePerUnitElementBundle\DependencyInjection\PricePerUnitElementExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PricePerUnitElementBundle extends AbstractAptoBundle
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
            'Apto\Plugins\PricePerUnitElement\Domain\Core\Model'
        );
    }

    /**
     * @return Extension
     */
    public function getExtension(): Extension
    {
        return new PricePerUnitElementExtension();
    }

}