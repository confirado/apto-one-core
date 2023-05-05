<?php

namespace Apto\Plugins\FloatInputElement\Infrastructure\FloatInputElementBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\FloatInputElement\Infrastructure\FloatInputElementBundle\DependencyInjection\FloatInputElementExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FloatInputElementBundle extends AbstractAptoBundle
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
            'Apto\Plugins\FloatInputElement\Domain\Core\Model'
        );
    }

    /**
     * @return Extension
     */
    public function getExtension(): Extension
    {
        return new FloatInputElementExtension();
    }
}