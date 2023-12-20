<?php

namespace Apto\Catalog\Domain\Core\Service\StateValidation;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class ValueValidationService
{
    private function getSectionListGroupedBy(State $state, string $fieldName): array
    {
        $sectionList = [];
        foreach ($state->getStateWithoutParameters() as $stateItem) {
            if (!isset($sectionList[$stateItem['sectionId'].$stateItem[$fieldName]] )) {
                $sectionList[$stateItem['sectionId'].$stateItem[$fieldName]] = [];
            }

            if (!$state->isParameter($stateItem['sectionId'])) {
                $sectionList[$stateItem['sectionId'].$stateItem[$fieldName]][] = $stateItem;
            }
        }
        return array_values($sectionList);
    }

    /**
     * Checks element value validity (values property in state)
     *
     * @param ConfigurableProduct $product
     * @param State               $state
     *
     * @return void
     * @throws InvalidUuidException
     */
    public function assertValidValues(ConfigurableProduct $product, State $state): void
    {
        foreach ($this->getSectionListGroupedBy($state, 'repetition') as $section) {
            $sectionId = $section[0]['sectionId'];
            $sectionUuid = new AptoUuid($sectionId);

            self::assertHasSection($product, $sectionUuid);

            // if multiple elements are NOT allowed in the section, but we have multiple in current section
            if (!$product->isSectionMultiple($sectionUuid) && count($section) > 1) {
                throw new InvalidStateException(
                    sprintf(
                        'The given section \'%s(%s)\' does not allow multiple elements in product \'%s(%s)\'.',
                        $sectionId,
                        $product->getSectionIdentifier($sectionUuid),
                        $product->getId()->getId(),
                        $product->getIdentifier()
                    ),
                    $product->getId()->getId(),
                    $sectionId
                );
            }

            foreach ($section as $element) {
                $elementId = $element['elementId'];
                $elementUuid = new AptoUuid($elementId);
                self::assertHasElement($product, $sectionUuid, $elementUuid);

                // skip elements without properties
                if (!empty($element['values'])) {
                    foreach ($element['values'] as $property => $value) {
                        self::assertHasProperty($product, $sectionUuid, $elementUuid, $property);
                        self::assertHasValue($product, $sectionUuid, $elementUuid, $property, $value);
                    }
                }
            }
        }
    }

    /**
     * Throw exception if given section is not contained in product
     * @param ConfigurableProduct $product
     * @param AptoUuid $sectionId
     * @throws InvalidUuidException
     */
    public function assertHasSection(ConfigurableProduct $product, AptoUuid $sectionId): void
    {
        if (!$product->hasSection($sectionId)) {
            throw new InvalidStateException(
                sprintf(
                    'The given section \'%s(%s)\' does not exist in product \'%s(%s)\'.',
                    $sectionId->getId(),
                    $product->getSectionIdentifier($sectionId),
                    $product->getId()->getId(),
                    $product->getIdentifier()
                ),
                $product->getId()->getId(),
                $sectionId->getId()
            );
        }
    }

    /**
     * Throw exception if given element is not contained in product
     * @param ConfigurableProduct $product
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @throws InvalidUuidException
     */
    public function assertHasElement(ConfigurableProduct $product, AptoUuid $sectionId, AptoUuid $elementId): void
    {
        if (!$product->hasElement($sectionId, $elementId)) {
            throw new InvalidStateException(
                sprintf(
                    'The given element \'%s(%s)\' in section \'%s(%s)\' does not exist in product \'%s(%s)\'.',
                    $elementId->getId(),
                    $product->getElementIdentifier($sectionId, $elementId),
                    $sectionId->getId(),
                    $product->getSectionIdentifier($sectionId),
                    $product->getId()->getId(),
                    $product->getIdentifier()
                ),
                $product->getId()->getId(),
                $sectionId->getId(),
                $elementId->getId()
            );
        }
    }

    /**
     * Throw exception if given property is not contained in product
     * @param ConfigurableProduct $product
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @throws InvalidUuidException
     */
    public function assertHasProperty(ConfigurableProduct $product, AptoUuid $sectionId, AptoUuid $elementId, string $property): void
    {
        if (!$product->hasProperty($sectionId, $elementId, $property)) {
            throw new InvalidStateException(
                sprintf(
                    'The given property \'%s\' in element \'%s(%s)\' and section \'%s(%s)\' does not exist in product \'%s(%s)\'.',
                    $property,
                    $elementId->getId(),
                    $product->getElementIdentifier($sectionId, $elementId),
                    $sectionId->getId(),
                    $product->getSectionIdentifier($sectionId),
                    $product->getId()->getId(),
                    $product->getIdentifier()
                ),
                $product->getId()->getId(),
                $sectionId->getId(),
                $elementId->getId(),
                $property
            );
        }
    }

    /**
     * @param ConfigurableProduct $product
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @param $value
     * @throws InvalidUuidException
     */
    public function assertHasValue(ConfigurableProduct $product, AptoUuid $sectionId, AptoUuid $elementId, string $property, $value): void
    {
        if (!$product->hasValue($sectionId, $elementId, $property, $value)) {
            throw new InvalidStateException(
                sprintf(
                    'The given value \'%s\' is not allowed for property \'%s\' in element \'%s(%s)\' and section \'%s(%s)\'.',
                    print_r($value, true),
                    $property,
                    $elementId->getId(),
                    $product->getElementIdentifier($sectionId, $elementId),
                    $sectionId->getId(),
                    $product->getSectionIdentifier($sectionId)
                ),
                $product->getId()->getId(),
                $sectionId->getId(),
                $elementId->getId(),
                $property,
                $value
            );
        }
    }
}
