<?php

namespace Apto\Plugins\PartsListElement\Application\Core\Service\ContentSnippet;

use Apto\Base\Application\Core\Service\ContentSnippet\AbstractContentSnippetProvider;

class PartsListElementContentSnippetProvider extends AbstractContentSnippetProvider
{
    /**
     * @return string
     */
    protected function getContentSnippetFilePath(): string
    {
        return __DIR__ . '/../../../../Infrastructure/PartsListElementBundle/Resources/content-snippets/content-snippet.json';
    }
}
