<?php

namespace Apto\Catalog\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslatedTypeNotMatchingException;
use Apto\Base\Application\Core\Service\Translation\TranslationImportProvider;
use Apto\Base\Application\Core\Service\Translation\TranslationItem;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyException;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;

class ProductTranslationImportProvider implements TranslationImportProvider
{
    const TRANSLATION_TYPE = 'Product';

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var string
     */
    private $translationType;

    /**
     * ProductTranslationImportProvider constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->translationType = self::TRANSLATION_TYPE;
    }

    /**
     * @param TranslationItem $translationItem
     * @throws AptoCustomPropertyException
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

        // Discount
        if ($fieldArray[1] === 'discount') {
            $currentDiscountName = $product->getAptoDiscountName($translationItem->getEntityId());
            if (null === $currentDiscountName) {
                $product->setAptoDiscountName($translationItem->getEntityId(), $translationItem->getTranslatedValue());
            } else {
                $product->setAptoDiscountName(
                    $translationItem->getEntityId(),
                    $currentDiscountName->merge($translationItem->getTranslatedValue())
                );
            }

            $this->productRepository->update($product);
            return;
        }

        // ProductRule
        if ($fieldArray[1] === 'rule') {
            $currentRuleErrorMessage = $product->getRuleErrorMessage($translationItem->getEntityId());
            if (null === $currentRuleErrorMessage) {
                $product->setRuleErrorMessage($translationItem->getEntityId(), $translationItem->getTranslatedValue());
            } else {
                $product->setRuleErrorMessage(
                    $translationItem->getEntityId(),
                    $currentRuleErrorMessage->merge($translationItem->getTranslatedValue())
                );
            }

            $this->productRepository->update($product);
            return;
        }

        // CustomProperty
        if ($fieldArray[1] === 'customProperty') {
            $customPropertyKey = $this->getCustomPropertyKey($fieldArray, 1);
            $currentCustomProperty = json_decode($product->getCustomProperty($customPropertyKey), true);

            if (null === $currentCustomProperty) {
                $product->setCustomProperty($customPropertyKey, $translationItem->getTranslatedValue(), true);
            } else {
                $product->setCustomProperty(
                    $customPropertyKey,
                    AptoTranslatedValue::fromArray($currentCustomProperty)->merge($translationItem->getTranslatedValue()),
                    true
                );
            }

            $this->productRepository->update($product);
            return;
        }

        // Section
        if ($fieldArray[1] === 'PS') {
            $sectionId = $product->getSectionIdByIdentifier(new Identifier($fieldArray[2]));
            if (null === $sectionId) {
                return;
            }

            // Discount
            if ($fieldArray[3] === 'discount') {
                $currentSectionDiscountName = $product->getSectionDiscountName($sectionId, $translationItem->getEntityId());
                if (null === $currentSectionDiscountName) {
                    $product->setSectionDiscountName($sectionId, $translationItem->getEntityId(), $translationItem->getTranslatedValue());
                } else {
                    $product->setSectionDiscountName(
                        $sectionId,
                        $translationItem->getEntityId(),
                        $currentSectionDiscountName->merge($translationItem->getTranslatedValue())
                    );
                }
            }

            // CustomProperty
            if ($fieldArray[3] === 'customProperty') {
                $customPropertyKey = $this->getCustomPropertyKey($fieldArray, 3);
                $currentSectionCustomProperty = json_decode($product->getSectionCustomProperty($sectionId, $customPropertyKey), true);

                $product->removeSectionCustomProperty($sectionId, $customPropertyKey);
                if (null === $currentSectionCustomProperty) {
                    $product->addSectionCustomProperty($sectionId, $customPropertyKey, $translationItem->getTranslatedValue(), true);
                } else {
                    $product->addSectionCustomProperty(
                        $sectionId,
                        $customPropertyKey,
                        AptoTranslatedValue::fromArray($currentSectionCustomProperty)->merge($translationItem->getTranslatedValue()),
                        true
                    );
                }
            }

            // SectionFields
            if ($fieldArray[3] === 'description') {
                $currentSectionDescription = $product->getSectionDescription($sectionId);
                if (null === $currentSectionDescription) {
                    $product->setSectionDescription(
                        $sectionId,
                        $translationItem->getTranslatedValue()
                    );
                } else {
                    $product->setSectionDescription(
                        $sectionId,
                        $currentSectionDescription->merge($translationItem->getTranslatedValue())
                    );
                }
            }
            if ($fieldArray[3] === 'name') {
                $currentSectionName = $product->getSectionName($sectionId);
                if (null === $currentSectionName) {
                    $product->setSectionName(
                        $sectionId,
                        $translationItem->getTranslatedValue()
                    );
                } else {
                    $product->setSectionName(
                        $sectionId,
                        $currentSectionName->merge($translationItem->getTranslatedValue())
                    );
                }
            }
            $this->productRepository->update($product);
            return;
        }

        // Element
        if ($fieldArray[1] === 'PSE') {
            $sectionId = $product->getSectionIdByIdentifier(new Identifier($fieldArray[2]));
            if (null === $sectionId) {
                return;
            }

            $elementId = $product->getElementIdByIdentifier($sectionId, new Identifier($fieldArray[3]));
            if (null === $elementId) {
                return;
            }

            // Discount
            if ($fieldArray[4] === 'discount') {
                $currentElementDiscountName = $product->getElementDiscountName($sectionId, $elementId, $translationItem->getEntityId());
                if (null === $currentElementDiscountName) {
                    $product->setElementDiscountName(
                        $sectionId,
                        $elementId,
                        $translationItem->getEntityId(),
                        $translationItem->getTranslatedValue()
                    );
                } else {
                    $product->setElementDiscountName(
                        $sectionId,
                        $elementId,
                        $translationItem->getEntityId(),
                        $currentElementDiscountName->merge($translationItem->getTranslatedValue())
                    );
                }
                $product->setElementDiscountName($sectionId, $elementId, $translationItem->getEntityId(), $translationItem->getTranslatedValue());
            }

            // CustomProperty
            if ($fieldArray[4] === 'customProperty') {
                $customPropertyKey = $this->getCustomPropertyKey($fieldArray, 4);
                $currentElementCustomProperty = json_decode($product->getElementCustomProperty($sectionId, $elementId, $customPropertyKey), true);

                $product->removeElementCustomProperty($sectionId, $elementId, $customPropertyKey);
                if (null === $currentElementCustomProperty) {
                    $product->addElementCustomProperty($sectionId, $elementId, $customPropertyKey, $translationItem->getTranslatedValue(), true);
                } else {
                    $product->addElementCustomProperty(
                        $sectionId,
                        $elementId,
                        $customPropertyKey,
                        AptoTranslatedValue::fromArray($currentElementCustomProperty)->merge($translationItem->getTranslatedValue()),
                        true
                    );
                }
            }

            // ElementFields
            if ($fieldArray[4] === 'errorMessage') {
                $currentElementErrorMessage = $product->getElementErrorMessage($sectionId, $elementId);
                if (null === $currentElementErrorMessage) {
                    $product->setElementErrorMessage(
                        $sectionId,
                        $elementId,
                        $translationItem->getTranslatedValue()
                    );
                } else {
                    $product->setElementErrorMessage(
                        $sectionId,
                        $elementId,
                        $currentElementErrorMessage->merge($translationItem->getTranslatedValue())
                    );
                }
            }
            if ($fieldArray[4] === 'name') {
                $currentElementName = $product->getElementName($sectionId, $elementId);
                if (null === $currentElementName) {
                    $product->setElementName(
                        $sectionId,
                        $elementId,
                        $translationItem->getTranslatedValue()
                    );
                } else {
                    $product->setElementName(
                        $sectionId,
                        $elementId,
                        $currentElementName->merge($translationItem->getTranslatedValue())
                    );
                }
            }
            if ($fieldArray[4] === 'description') {
                $currentElementDescription = $product->getElementDescription($sectionId, $elementId);
                if (null === $currentElementDescription) {
                    $product->setElementDescription(
                        $sectionId,
                        $elementId,
                        $translationItem->getTranslatedValue()
                    );
                } else {
                    $product->setElementDescription(
                        $sectionId,
                        $elementId,
                        $currentElementDescription->merge($translationItem->getTranslatedValue())
                    );
                }
            }
            $this->productRepository->update($product);
            return;
        }

        // Product Fields
        if ($fieldArray[1] === 'metaDescription') {
            $product->setMetaDescription($product->getMetaDescription()->merge($translationItem->getTranslatedValue()));
        }
        if ($fieldArray[1] === 'metaTitle') {
            $product->setMetaTitle($product->getMetaTitle()->merge($translationItem->getTranslatedValue()));
        }
        if ($fieldArray[1] === 'name') {
            $product->setName($product->getName()->merge($translationItem->getTranslatedValue()));
        }
        if ($fieldArray[1] === 'description') {
            $product->setDescription($product->getDescription()->merge($translationItem->getTranslatedValue()));
        }
        $this->productRepository->update($product);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->translationType;
    }

    /**
     * @param array $fields
     * @param int $index
     * @return string
     */
    private function getCustomPropertyKey(array $fields, int $index): string
    {
        $values = [];
        foreach ($fields as $key => $value) {
            if ($key <= $index) {
                continue;
            }
            $values[] = $value;
        }

        return implode('_', $values);
    }
}