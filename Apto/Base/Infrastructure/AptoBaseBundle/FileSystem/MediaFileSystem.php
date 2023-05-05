<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\FileSystem;

use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemInvalidRootDirectoryException;

class MediaFileSystem extends LocalFileSystem implements MediaFileSystemConnector
{
    /**
     * @param AptoParameterInterface $aptoParameter
     * @throws FileSystemInvalidRootDirectoryException
     */
    public function __construct(AptoParameterInterface $aptoParameter)
    {
        parent::__construct(
            $aptoParameter,
            $aptoParameter->get('media_directory'),
            $aptoParameter->get('media_relative_path')
        );
    }
}