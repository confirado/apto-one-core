<?php

namespace Apto\Catalog\Application\Core\Service\Translation;

use Apto\Catalog\Domain\Core\Model\Category\CategoryRepository;
use Apto\Base\Application\Core\Service\Translation\TranslationImportProvider;
use Apto\Base\Application\Core\Service\Translation\TranslationItem;
use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslatedTypeNotMatchingException;

class CategoryTranslationImportProvider implements TranslationImportProvider
{
    const TRANSLATION_TYPE = 'Category';

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var string
     */
    private $translationType;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
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

        $categoryEntity = $this->categoryRepository->findById($translationItem->getEntityId()->getId());
        if (null === $categoryEntity) {
            return;
        }

        $fieldNameArray = explode('_', $translationItem->getFieldName());
        if ($fieldNameArray[1] === 'name' ) {
            $categoryEntity->setName($categoryEntity->getName()->merge($translationItem->getTranslatedValue()));
        }
        if ($fieldNameArray[1] === 'description' ) {
            $categoryEntity->setDescription($categoryEntity->getDescription()->merge($translationItem->getTranslatedValue()));
        }

        $this->categoryRepository->update($categoryEntity);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->translationType;
    }
}