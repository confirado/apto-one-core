<?php

namespace Apto\Base\Application\Core\Service\ContentSnippet;

interface ContentSnippetProvider
{
    /**
     * @return string
     */
    public function getContentSnippetsJson(): string;
}