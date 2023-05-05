<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\FileSystem;

use Apto\Base\Domain\Core\Model\FileSystem\ThumbFileSystemConnector;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemInvalidRootDirectoryException;

class ThumbFileSystem extends LocalFileSystem implements ThumbFileSystemConnector
{
    /**
     * @param AptoParameterInterface $aptoParameter
     * @throws FileSystemInvalidRootDirectoryException
     */
    public function __construct(AptoParameterInterface $aptoParameter)
    {
        parent::__construct(
            $aptoParameter,
            $aptoParameter->get('thumb_directory'),
            $aptoParameter->get('thumb_relative_path')
        );
    }
}