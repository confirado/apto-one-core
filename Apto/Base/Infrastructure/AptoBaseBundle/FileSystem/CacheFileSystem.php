<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\FileSystem;

use Apto\Base\Domain\Core\Model\FileSystem\CacheFileSystemConnector;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemInvalidRootDirectoryException;

class CacheFileSystem extends LocalFileSystem implements CacheFileSystemConnector
{
    /**
     * @param AptoParameterInterface $aptoParameter
     * @throws FileSystemInvalidRootDirectoryException
     */
    public function __construct(AptoParameterInterface $aptoParameter)
    {
        parent::__construct(
            $aptoParameter,
            $aptoParameter->get('cache_directory'),
            $aptoParameter->get('cache_relative_path')
        );
    }
}