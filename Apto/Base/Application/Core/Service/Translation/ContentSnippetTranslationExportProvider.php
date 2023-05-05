<?php

namespace Apto\Base\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Query\ContentSnippet\ContentSnippetFinder;
use Apto\Base\Domain\Core\Model\InvalidUuidException;

class ContentSnippetTranslationExportProvider extends AbstractTranslationExportProvider
{
    const TRANSLATION_TYPE = 'ContentSnippet';

    /**
     * @var ContentSnippetFinder
     */
    private $contentSnippetFinder;

    /**
     * @param ContentSnippetFinder $contentSnippetFinder
     * @throws Exceptions\TranslationTypeNotFoundException
     */
    public function __construct(ContentSnippetFinder $contentSnippetFinder)
    {
        parent::__construct();
        $this->contentSnippetFinder = $contentSnippetFinder;
    }

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getTranslatedValues(): array
    {
        $snippets = $this->contentSnippetFinder->getExportItems();
        $translationItems = [];
        foreach ($snippets as $id => $snippet) {
            $translationItems[] = $this->makeTranslationItem($snippet['snippetPath'], $snippet['value'], $id);
        }
        return $translationItems;
    }
}