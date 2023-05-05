<?php

namespace Apto\Base\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslatedTypeNotMatchingException;
use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslationTypeNotFoundException;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;

abstract class AbstractSpecialElementTranslationImportProvider implements TranslationImportProvider
{
    const TRANSLATABLE_FIELDS = [];
    const TRANSLATION_TYPE = 'SET_TRANSLATION_TYPE_IN_CHILD_CLASS';

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var AptoJsonSerializer
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $translatableFields;

    /**
     * @var string
     */
    protected $translationType;

    /**
     * @param ProductRepository $productRepository
     * @param AptoJsonSerializer $serializer
     * @throws TranslationTypeNotFoundException
     */
    public function __construct(
        ProductRepository $productRepository,
        AptoJsonSerializer $serializer
    ) {
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
        $this->translationType = static::TRANSLATION_TYPE;
        $this->translatableFields = static::TRANSLATABLE_FIELDS;

        if ($this->translationType === self::TRANSLATION_TYPE) {
            throw new TranslationTypeNotFoundException('TranslationType not set in child class.');
        }
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

        $fieldName = $translationItem->getFieldName();
        $fieldArray = explode('_', $fieldName);
        $product = $this->productRepository->findByIdentifier(new Identifier($fieldArray[0]));
        if (null === $product) {
            return;
        }

        $sectionId = $product->getSectionIdByIdentifier(new Identifier($fieldArray[1]));
        if (null === $sectionId) {
            return;
        }

        $elementId = $translationItem->getEntityId();
        $elementDefinition = $product->getElementDefinition($sectionId, $elementId);
        if (null === $elementDefinition) {
            return;
        }

        $elementDefinitionJSON = json_decode($this->serializer->jsonSerialize($elementDefinition), true);
        foreach ($this->translatableFields as $translatableField) {
            if ($fieldArray['3'] === $translatableField) {
                $elementDefinitionJSON['json'][$translatableField] = array_merge(
                    $elementDefinitionJSON['json'][$translatableField] ?? [],
                    $translationItem->getTranslatedValue()->jsonSerialize()
                );
                break;
            }
        }

        /** @var ElementDefinition $elementDefinition */
        $elementDefinition = $this->serializer->jsonUnSerialize(json_encode($elementDefinitionJSON, JSON_UNESCAPED_UNICODE));

        $product->setElementDefinition($sectionId, $elementId, $elementDefinition);
        $this->productRepository->update($product);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->translationType;
    }
}
