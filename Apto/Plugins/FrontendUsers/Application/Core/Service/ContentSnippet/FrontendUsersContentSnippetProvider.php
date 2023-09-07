<?php

namespace Apto\Plugins\FrontendUsers\Application\Core\Service\ContentSnippet;

use Apto\Base\Application\Core\Service\ContentSnippet\ContentSnippetProvider;
use Apto\Base\Application\Core\Service\ContentSnippet\Exceptions\ContentSnippetJsonFileNotFoundException;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\RootFileSystemConnector;

class FrontendUsersContentSnippetProvider implements ContentSnippetProvider
{
    private RootFileSystemConnector $rootConnector;

    /**
     * @param RootFileSystemConnector $rootConnector
     */
    public function __construct(RootFileSystemConnector $rootConnector)
    {
        $this->rootConnector = $rootConnector;
    }

    /**
     * @return string
     * @throws ContentSnippetJsonFileNotFoundException
     */
    public function getContentSnippetsJson(): string
    {
        $file = File::createFromPath(realpath(__DIR__ . '/../../content-snippet.json'));

        if ($this->rootConnector->existsFile($file)) {
            return $this->rootConnector->getFileContent($file);
        } else {
            throw new ContentSnippetJsonFileNotFoundException();
        }
    }
}
