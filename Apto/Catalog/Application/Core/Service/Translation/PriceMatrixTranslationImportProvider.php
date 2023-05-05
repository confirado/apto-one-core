<?php

namespace Apto\Catalog\Application\Core\Service\Translation;

use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixRepository;
use Apto\Base\Application\Core\Service\Translation\TranslationImportProvider;
use Apto\Base\Application\Core\Service\Translation\TranslationItem;
use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslatedTypeNotMatchingException;

class PriceMatrixTranslationImportProvider implements TranslationImportProvider
{
    const TRANSLATION_TYPE = 'PriceMatrix';

    /**
     * @var PriceMatrixRepository
     */
    private $priceMatrixRepository;

    /**
     * @var string
     */
    private $translationType;

    /**
     * @param PriceMatrixRepository $priceMatrixRepository
     */
    public function __construct(PriceMatrixRepository $priceMatrixRepository)
    {
        $this->priceMatrixRepository = $priceMatrixRepository;
        $this->translationType = self::TRANSLATION_TYPE;
    }

    /**
     * @param TranslationItem $translationItem
     * @throws TranslatedTypeNotMatchingException
     */
    public function setTranslatedValue(TranslationItem $translationItem): void
    {
        if ($translationItem->getTranslationType() !== $this->translationType) {
            throw new TranslatedTypeNotMatchingException($translationItem->getTranslationType(), $this->translationType);
        }

        $priceMatrixEntity = $this->priceMatrixRepository->findById($translationItem->getEntityId()->getId());
        if (null === $priceMatrixEntity) {
            return;
        }

        $priceMatrixEntity->setName($priceMatrixEntity->getName()->merge($translationItem->getTranslatedValue()));
        $this->priceMatrixRepository->update($priceMatrixEntity);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->translationType;
    }
}