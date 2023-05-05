<?php

namespace Apto\Plugins\AreaElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\AbstractSpecialElementTranslationImportProvider;
use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslatedTypeNotMatchingException;
use Apto\Base\Application\Core\Service\Translation\TranslationItem;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;

class AreaElementTranslationImportProvider extends AbstractSpecialElementTranslationImportProvider
{
    const TRANSLATABLE_FIELDS = ['livePricePrefix', 'livePriceSuffix'];
    const TRANSLATION_TYPE = 'AreaElement';

    /**
     * @param TranslationItem $translationItem
     * @return void
     * @throws TranslatedTypeNotMatchingException
     * @throws AptoJsonSerializerException
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
        if (count($fieldArray) === 4) {
            foreach ($this->translatableFields as $translatableField) {
                if ($fieldArray[3] === $translatableField) {
                    $elementDefinitionJSON['json'][$translatableField] = array_merge(
                        $elementDefinitionJSON['json'][$translatableField] ?? [],
                        $translationItem->getTranslatedValue()->jsonSerialize()
                    );
                    break;
                }
            }
        }

        if (count($fieldArray) === 5) {
            $elementDefinitionJSON['json']['fields'][$fieldArray[3]][$fieldArray[4]] = array_merge(
                $elementDefinitionJSON['json']['fields'][$fieldArray[3]][$fieldArray[4]] ?? [],
                $translationItem->getTranslatedValue()->jsonSerialize()
            );
        }

        /** @var ElementDefinition $elementDefinition */
        $elementDefinition = $this->serializer->jsonUnSerialize(json_encode($elementDefinitionJSON));

        $product->setElementDefinition($sectionId, $elementId, $elementDefinition);
        $this->productRepository->update($product);
    }
}