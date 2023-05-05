<?php

namespace Apto\Base\Application\Core\Service\ContentSnippet;

class AptoBaseContentSnippetProvider extends AbstractContentSnippetProvider
{
    /**
     * @return string
     */
    protected function getContentSnippetFilePath(): string
    {
        return __DIR__ . '/../../../../Infrastructure/AptoBaseBundle/Resources/content-snippets/content-snippets.json';
    }
}
