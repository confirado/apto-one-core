<?php

namespace Apto\Base\Application\Core\Service\Translation;

use Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippetParentException;
use Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippetRepository;

class ContentSnippetTranslationImportProvider implements TranslationImportProvider
{
    const TRANSLATION_TYPE = 'ContentSnippet';

    /**
     * @var ContentSnippetRepository
     */
    private $contentSnippetRepository;

    /**
     * @var string
     */
    private $translationType;

    /**
     * @param ContentSnippetRepository $contentSnippetRepository
     */
    public function __construct(ContentSnippetRepository $contentSnippetRepository)
    {
        $this->contentSnippetRepository = $contentSnippetRepository;
        $this->translationType = self::TRANSLATION_TYPE;
    }

    /**
     * @param TranslationItem $translationItem
     * @throws ContentSnippetParentException
     * @throws Exceptions\TranslatedTypeNotMatchingException
     */
    public function setTranslatedValue(TranslationItem $translationItem): void
    {
        if ($translationItem->getTranslationType() !== $this->translationType) {
            throw new Exceptions\TranslatedTypeNotMatchingException($translationItem->getTranslationType(), $this->translationType);
        }
        if ($this->contentSnippetRepository->hasChildren($translationItem->getEntityId()->getId())) {
            throw new ContentSnippetParentException('Snippet Folder cannot have content');
        }

        $contentSnippetEntity = $this->contentSnippetRepository->findById($translationItem->getEntityId()->getId());
        if (null === $contentSnippetEntity) {
            return;
        }

        $contentSnippetEntity->setContent(
            $contentSnippetEntity->getContent()->merge(
                $translationItem->getTranslatedValue()
            )
        );
        $this->contentSnippetRepository->update($contentSnippetEntity);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->translationType;
    }
}