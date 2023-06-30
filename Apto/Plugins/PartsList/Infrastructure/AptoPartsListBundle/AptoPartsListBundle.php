<?php

namespace Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle\DependencyInjection\AptoPartsListExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AptoPartsListBundle
 * @package Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle
 */
class AptoPartsListBundle extends AbstractAptoBundle
{
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        $this->addDoctrineOrmMapping($container, __DIR__ . '/Resources/doctrine/orm', 'Apto\Plugins\PartsList\Domain\Core\Model');
    }

    /**
     * @return AptoPartsListExtension
     */
    public function getExtension()
    {
        return new AptoPartsListExtension();
    }
}