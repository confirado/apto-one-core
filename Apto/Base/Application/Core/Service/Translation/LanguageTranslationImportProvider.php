<?php

namespace Apto\Base\Application\Core\Service\Translation;

use Apto\Base\Domain\Core\Model\Language\LanguageRepository;

class LanguageTranslationImportProvider implements TranslationImportProvider
{
    const TRANSLATION_TYPE = 'Language';

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * @var string
     */
    private $translationType;

    /**
     * @param LanguageRepository $languageRepository
     */
    public function __construct(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
        $this->translationType = self::TRANSLATION_TYPE;
    }

    /**
     * @param TranslationItem $translationItem
     * @throws Exceptions\TranslatedTypeNotMatchingException
     */
    public function setTranslatedValue(TranslationItem $translationItem): void
    {
        if ($translationItem->getTranslationType() !== $this->translationType) {
            throw new Exceptions\TranslatedTypeNotMatchingException($translationItem->getTranslationType(), $this->translationType);
        }

        $languageEntity = $this->languageRepository->findById($translationItem->getEntityId()->getId());
        if (null === $languageEntity) {
            return;
        }

        $languageEntity->setName(
            $languageEntity->getName()->merge($translationItem->getTranslatedValue())
        );
        $this->languageRepository->update($languageEntity);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->translationType;
    }
}