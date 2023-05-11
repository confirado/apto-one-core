<?php

namespace Apto\Plugins\ImageUpload\Infrastructure\ImageUploadBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\ImageUpload\Infrastructure\ImageUploadBundle\DependencyInjection\ImageUploadExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class ImageUploadBundle extends AbstractAptoBundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $this->addDoctrineOrmMapping($container, __DIR__ . '/Resources/doctrine/canvas', 'Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas');
    }

    /**
     * @return Extension
     */
    public function getExtension()
    {
        return new ImageUploadExtension();
    }
}
