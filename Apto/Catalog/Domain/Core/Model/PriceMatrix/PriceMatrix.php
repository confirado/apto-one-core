<?php
namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoCustomProperty;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Money\Currency;
use Money\Money;

class PriceMatrix extends AptoAggregate
{
    /**
     * @var AptoTranslatedValue
     */
    private $name;

    /**
     * @var Collection
     */
    private $elements;

    /**
     * PriceMatrix constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->name = $name;
        $this->elements = new ArrayCollection();
        $this->publish(
            new PriceMatrixAdded(
                $id,
                $name
            )
        );
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @param AptoTranslatedValue $name
     * @return PriceMatrix
     */
    public function setName(AptoTranslatedValue $name): PriceMatrix
    {
        if ($this->getName()->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new PriceMatrixNameUpdated(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
    }

    /**
     * @param PriceMatrixPosition $priceMatrixPosition
     * @return PriceMatrix
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function addPriceMatrixElement(PriceMatrixPosition $priceMatrixPosition): PriceMatrix
    {
        $this->assertUniquePriceMatrixPosition($priceMatrixPosition);

        $priceMatrixElementId = $this->nextElementId();
        $this->elements->set(
            $priceMatrixElementId->getId(),
            new PriceMatrixElement(
                $priceMatrixElementId,
                $this,
                $priceMatrixPosition
            )
        );

        $this->publish(
            new PriceMatrixElementAdded(
                $this->getId(),
                $priceMatrixElementId,
                $priceMatrixPosition
            )
        );

        return $this;
    }

    /**
     * @param PriceMatrixPosition $priceMatrixPosition
     * @return null|AptoUuid
     */
    public function getPriceMatrixElementIdByPosition(PriceMatrixPosition $priceMatrixPosition)
    {
        foreach ($this->elements as $element) {
            /** @var PriceMatrixElement $element */
            if ($element->getPosition()->equals($priceMatrixPosition)) {
                return $element->getId();
            }
        }

        return null;
    }

    /**
     * @param AptoUuid $priceMatrixElementId
     * @return PriceMatrix
     */
    public function removePriceMatrixElement(AptoUuid $priceMatrixElementId): PriceMatrix
    {
        if (!$this->hasElement($priceMatrixElementId)) {
            return $this;
        }

        $this->elements->remove($priceMatrixElementId->getId());

        $this->publish(
            new PriceMatrixElementRemoved(
                $this->getId(),
                $priceMatrixElementId
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param Money $price
     * @param AptoUuid $customerGroupId
     * @return PriceMatrix
     * @throws \Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function addPriceMatrixElementPrice(AptoUuid $elementId, Money $price, AptoUuid $customerGroupId): PriceMatrix
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($elementId);
        if (null === $element) {
            return $this;
        }

        // add price from element
        $element->addAptoPrice($price, $customerGroupId);

        $this->publish(
            new PriceMatrixElementPriceAdded(
                $this->getId(),
                $elementId,
                $customerGroupId,
                $price
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param AptoUuid $priceId
     * @return PriceMatrix
     */
    public function removePriceMatrixElementPrice(AptoUuid $elementId, AptoUuid $priceId): PriceMatrix
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($elementId);
        if (null === $element) {
            return $this;
        }

        // remove price from element
        $element->removeAptoPrice($priceId);

        $this->publish(
            new PriceMatrixElementPriceRemoved(
                $this->getId(),
                $priceId
            )
        );

        return $this;
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     */
    public function removePriceMatrixElementPricesByCustomerGroup(Currency $currency, AptoUuid $customerGroupId)
    {
        /** @var PriceMatrixElement $element */
        foreach ($this->elements as $element) {
            $priceId = $element->getAptoPriceId($currency, $customerGroupId);
            if (null !== $priceId) {
                $element->removeAptoPrice($priceId);
            }
        }
    }

    /**
     * @param AptoUuid $elementId
     * @param string $key
     * @param string $value
     * @param bool $translatable
     * @return PriceMatrix
     * @throws \Apto\Base\Domain\Core\Model\AptoCustomPropertyException
     */
    public function addPriceMatrixElementCustomProperty(AptoUuid $elementId, string $key, string $value, bool $translatable = false): PriceMatrix
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($elementId);
        if (null === $element) {
            return $this;
        }

        // add price from element
        $element->setCustomProperty($key, $value, $translatable);

        $this->publish(
            new PriceMatrixElementCustomPropertyAdded(
                $this->getId(),
                $elementId,
                $key,
                $value,
                $translatable
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param string $key
     * @return PriceMatrix
     */
    public function removePriceMatrixElementCustomProperty(AptoUuid $elementId, string $key): PriceMatrix
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($elementId);
        if (null === $element) {
            return $this;
        }

        // remove price from element
        $element->removeCustomProperty($key);

        $this->publish(
            new PriceMatrixElementCustomPropertyRemoved(
                $this->getId(),
                $key
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param string $key
     * @return null|string
     */
    public function getPriceMatrixElementCustomProperty(AptoUuid $elementId, string $key)
    {
        $element = $this->getElement($elementId);
        return null === $element ? null : $element->getCustomProperty($key);
    }

    /**
     * @param AptoUuid $elementId
     * @param string $key
     * @return bool
     */
    public function hasPriceMatrixElementCustomProperty(AptoUuid $elementId, string $key): bool
    {
        $element = $this->getElement($elementId);
        return null === $element ? false : $element->hasCustomProperty($key);
    }

    /**
     * @param AptoUuid $elementId
     */
    public function clearPriceMatrixElementCustomProperties(AptoUuid $elementId)
    {
        $element = $this->getElement($elementId);
        if (null !== $element) {
            $element->clearCustomProperties();
        }
    }

    /**
     * @hint remove Elements without prices but ignore existing CustomProperties
     */
    public function removePriceMatrixElementsWithoutPrices()
    {
        /** @var PriceMatrixElement $element */
        foreach ($this->elements as $element) {
            if (!$element->hasAptoPrices()) {
                // ignore $element->hasCustomProperties()
                $this->elements->removeElement($element);
            }
        }
    }

    /**
     * @param PriceMatrixPosition $priceMatrixPosition
     */
    private function assertUniquePriceMatrixPosition(PriceMatrixPosition $priceMatrixPosition)
    {
        if ($this->getPriceMatrixElementIdByPosition($priceMatrixPosition)) {
            throw new \InvalidArgumentException('Matrix position \'Column:' . $priceMatrixPosition->getColumnValue() . '|Row:' . $priceMatrixPosition->getRowValue() . '\' already exists.');
        }
    }

    /**
     * @param AptoUuid $elementId
     * @return PriceMatrixElement|null
     */
    private function getElement(AptoUuid $elementId)
    {
        if ($this->hasElement($elementId)) {
            return $this->elements->get($elementId->getId());
        }

        return null;
    }

    /**
     * @param AptoUuid $elementId
     * @return bool
     */
    private function hasElement(AptoUuid $elementId): bool
    {
        return $this->elements->containsKey($elementId->getId());
    }

    /**
     * @return AptoUuid
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    private function nextElementId(): AptoUuid
    {
        return new AptoUuid();
    }
}