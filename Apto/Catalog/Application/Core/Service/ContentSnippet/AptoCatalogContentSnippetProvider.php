<?php

namespace Apto\Catalog\Application\Core\Service\ContentSnippet;

use Apto\Base\Application\Core\Service\ContentSnippet\AbstractContentSnippetProvider;

class AptoCatalogContentSnippetProvider extends AbstractContentSnippetProvider
{
    /**
     * @return string
     */
    protected function getContentSnippetFilePath(): string
    {
        return __DIR__ . '/../../../../Infrastructure/AptoCatalogBundle/Resources/content-snippets/content-snippets.json';
    }
}
