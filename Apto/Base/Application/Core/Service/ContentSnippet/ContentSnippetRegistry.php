<?php

namespace Apto\Base\Application\Core\Service\ContentSnippet;

class ContentSnippetRegistry
{
    /**
     * @var string
     */
    private $contentSnippetProviders;


    /**
     * ContentSnippetRegistry constructor.
     */
    public function __construct()
    {
        $this->contentSnippetProviders = [];
    }

    /**
     * @param ContentSnippetProvider $contentSnippetProvider
     * @return void
     */
    public function addContentSnippetProvider(ContentSnippetProvider $contentSnippetProvider)
    {
        $className = get_class($contentSnippetProvider);

        if (array_key_exists($className, $this->contentSnippetProviders)) {
            throw new \InvalidArgumentException('A ContentSnippet provider with an id \'' . $className . '\' is already registered.');
        }

        $this->contentSnippetProviders[$className] = $contentSnippetProvider;
    }

    /**
     * @return array
     */
    public function getContentSnippetProviders(): array
    {
        return $this->contentSnippetProviders;
    }
}