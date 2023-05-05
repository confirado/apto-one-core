<?php

namespace Apto\Plugins\SelectBoxElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslatedTypeNotMatchingException;
use Apto\Base\Application\Core\Service\Translation\TranslationImportProvider;
use Apto\Base\Application\Core\Service\Translation\TranslationItem;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItemRepository;

class SelectBoxElementTranslationImportProvider implements TranslationImportProvider
{
    const TRANSLATION_TYPE = 'SelectBoxElement';

    /**
     * @var SelectBoxItemRepository
     */
    private $selectBoxItemRepository;

    /**
     * @var string
     */
    private $translationType;

    /**
     * @param SelectBoxItemRepository $selectBoxItemRepository
     */
    public function __construct(SelectBoxItemRepository $selectBoxItemRepository)
    {
        $this->selectBoxItemRepository = $selectBoxItemRepository;
        $this->translationType = self::TRANSLATION_TYPE;
    }

    /**
     * @param TranslationItem $translationItem
     * @throws TranslatedTypeNotMatchingException
     * @throws \Exception
     */
    public function setTranslatedValue(TranslationItem $translationItem): void
    {
        if ($translationItem->getTranslationType() !== $this->translationType) {
            throw new TranslatedTypeNotMatchingException($translationItem->getTranslationType(), $this->translationType);
        }

        $selectBoxItem = $this->selectBoxItemRepository->findById($translationItem->getEntityId()->getId());
        if (null === $selectBoxItem) {
            return;
        }

        $selectBoxItem->setName($selectBoxItem->getName()->merge($translationItem->getTranslatedValue()));
        $this->selectBoxItemRepository->update($selectBoxItem);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->translationType;
    }
}