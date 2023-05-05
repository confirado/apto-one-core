<?php

namespace Apto\Plugins\FileUpload\Infrastructure\FileUploadBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\FileUpload\Infrastructure\FileUploadBundle\DependencyInjection\FileUploadExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FileUploadBundle extends AbstractAptoBundle
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
        return new FileUploadExtension();
    }
}