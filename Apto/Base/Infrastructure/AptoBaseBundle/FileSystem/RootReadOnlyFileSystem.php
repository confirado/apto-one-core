<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\FileSystem;

use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemInvalidRootDirectoryException;
use Apto\Base\Domain\Core\Model\FileSystem\RootReadOnlyFileSystemConnector;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;

class RootReadOnlyFileSystem extends LocalFileSystem implements RootReadOnlyFileSystemConnector
{
    /**
     * @param AptoParameterInterface $aptoParameter
     * @throws FileSystemInvalidRootDirectoryException
     */
    public function __construct(AptoParameterInterface $aptoParameter)
    {
        parent::__construct(
            $aptoParameter,
            $aptoParameter->get('root_directory'),
            '',
            true
        );
    }
}