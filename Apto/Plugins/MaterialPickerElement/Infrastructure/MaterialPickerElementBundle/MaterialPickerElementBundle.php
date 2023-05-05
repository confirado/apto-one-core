<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\DependencyInjection\MaterialPickerElementExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MaterialPickerElementBundle extends AbstractAptoBundle
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
            'Apto\Plugins\MaterialPickerElement\Domain\Core\Model'
        );
    }

    /**
     * @return Extension
     */
    public function getExtension(): Extension
    {
        return new MaterialPickerElementExtension();
    }
}