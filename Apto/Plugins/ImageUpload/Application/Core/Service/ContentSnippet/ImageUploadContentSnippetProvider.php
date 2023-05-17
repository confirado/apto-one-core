<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Service\ContentSnippet;

use Apto\Base\Application\Core\Service\ContentSnippet\AbstractContentSnippetProvider;

class ImageUploadContentSnippetProvider extends AbstractContentSnippetProvider
{
    /**
     * @return string
     */
    protected function getContentSnippetFilePath(): string
    {
        return __DIR__ . '/../../../../Infrastructure/ImageUploadBundle/Resources/content-snippets/content-snippets.json';
    }
}
