<?php

namespace Apto\Catalog\Domain\Core\Service\StateValidation;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class ValueValidationService
{
    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @throws InvalidUuidException
     */
    public function assertValidValues(ConfigurableProduct $product, State $state)
    {
        foreach ($state->getStateWithoutParameters() as $section) {

            $sectionId = $section['sectionId'];
            $sectionUuid = new AptoUuid($sectionId);
            self::assertHasSection($product, $sectionUuid);

            $elementsInSection = $state->getSectionElements($sectionUuid);

            // check, if multiple elements are allowed in current section
            if (!$product->isSectionMultiple($sectionUuid) && count($elementsInSection) > 1) {
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

            $elementId = $section['elementId'];
            $elementUuid = new AptoUuid($elementId);
            self::assertHasElement($product, $sectionUuid, $elementUuid);

            // skip elements without properties
            if (!empty($section['values'])) {
                foreach ($section['values'] as $property => $value) {
                    self::assertHasProperty($product, $sectionUuid, $elementUuid, $property);
                    self::assertHasValue($product, $sectionUuid, $elementUuid, $property, $value);
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
    public function assertHasSection(ConfigurableProduct $product, AptoUuid $sectionId)
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
    public function assertHasElement(ConfigurableProduct $product, AptoUuid $sectionId, AptoUuid $elementId)
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
    public function assertHasProperty(ConfigurableProduct $product, AptoUuid $sectionId, AptoUuid $elementId, string $property)
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
    public function assertHasValue(ConfigurableProduct $product, AptoUuid $sectionId, AptoUuid $elementId, string $property, $value)
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
