<?php

namespace Apto\Base\Application\Core\Service\ContentSnippet\Exceptions;

class ContentSnippetJsonFileNotFoundException extends \Exception
{
    /**
     * ContentSnippetJsonFileNotFoundException constructor.
     */
    public function __construct()
    {
        $message = 'No content-snippet.json File found';
        parent::__construct($message);
    }
}