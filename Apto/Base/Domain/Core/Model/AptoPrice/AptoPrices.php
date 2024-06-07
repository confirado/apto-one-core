<?php

namespace Apto\Base\Domain\Core\Model\AptoPrice;

use Apto\Base\Domain\Core\Model\AptoEventCapableEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Money\Currency;
use Money\Money;

/**
 * Trait AptoPrices
 * @package Apto\Base\Domain\Core\Model\AptoPrice
 */
trait AptoPrices
{
    /**
     * @var Collection
     */
    protected $aptoPrices;

    /**
     * @param Money $price
     * @param AptoUuid $customerGroupId
     * @return self
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    public function addAptoPrice(Money $price, AptoUuid $customerGroupId, ?AptoUuid $productConditionId = null): self
    {
        $priceId = $this->nextAptoPriceId();
        if ($this->containsAptoPriceId($priceId)) {
            return $this;
        }

        $this->assertNoAptoPriceDuplicates($price->getCurrency(), $customerGroupId, $productConditionId, $priceId);

        $this->aptoPrices->set(
            $priceId->getId(),
            new AptoPrice(
                $priceId,
                $price,
                $customerGroupId,
                $productConditionId
            )
        );

        $this->publishAptoPriceEventIfCapable(
            new AptoPriceAdded(
                $priceId,
                $this->getId(),
                $price,
                $customerGroupId,
                $productConditionId
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $priceId
     * @return self
     */
    public function removeAptoPrice(AptoUuid $priceId): self
    {
        if (!$this->containsAptoPriceId($priceId)) {
            return $this;
        }

        $this->aptoPrices->remove($priceId->getId());
        $this->publishAptoPriceEventIfCapable(
            new AptoPriceRemoved(
                $priceId,
                $this->getId()
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $priceId
     * @param Money $price
     * @return self
     * @throws AptoPriceDuplicateException
     */
    public function setAptoPricePrice(AptoUuid $priceId, Money $price): self
    {
        if (!$this->containsAptoPriceId($priceId)) {
            return $this;
        }

        /** @var AptoPrice $aptoPrice */
        $aptoPrice = $this->aptoPrices->get($priceId->getId());

        if (!$aptoPrice->getPrice()->equals($price)) {
            $this->assertNoAptoPriceDuplicates($price->getCurrency(), $aptoPrice->getCustomerGroupId(), $aptoPrice->getProductConditionId(), $priceId);

            $aptoPrice->setPrice($price);
            $this->publishAptoPriceEventIfCapable(
                new AptoPricePriceChanged(
                    $priceId,
                    $this->getId(),
                    $aptoPrice->getPrice()
                )
            );
        }

        return $this;
    }

    /**
     * @param AptoUuid $priceId
     * @param AptoUuid $customerGroupId
     * @return self
     * @throws AptoPriceDuplicateException
     */
    public function setAptoPriceCustomerGroupId(AptoUuid $priceId, AptoUuid $customerGroupId): self
    {
        if (!$this->containsAptoPriceId($priceId)) {
            return $this;
        }

        /** @var AptoPrice $aptoPrice */
        $aptoPrice = $this->aptoPrices->get($priceId->getId());

        if ($aptoPrice->getCustomerGroupId()->getId() !== $customerGroupId->getId()) {
            $this->assertNoAptoPriceDuplicates($aptoPrice->getPrice()->getCurrency(), $customerGroupId, $aptoPrice->getProductConditionId(), $priceId);

            $aptoPrice->setCustomerGroupId($customerGroupId);

            $this->publishAptoPriceEventIfCapable(
                new AptoPriceCustomerGroupIdChanged(
                    $priceId,
                    $this->getId(),
                    $customerGroupId
                )
            );
        }

        return $this;
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return Money|null
     */
    public function getAptoPrice(Currency $currency, AptoUuid $customerGroupId)
    {
        $aptoPrice = $this->getAptoPriceObject($currency, $customerGroupId);
        return null !== $aptoPrice ? $aptoPrice->getPrice() : null;
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return AptoUuid|null
     */
    public function getAptoPriceId(Currency $currency, AptoUuid $customerGroupId)
    {
        $aptoPrice = $this->getAptoPriceObject($currency, $customerGroupId);
        return null !== $aptoPrice ? $aptoPrice->getId() : null;
    }

    /**
     * @return bool
     */
    public function hasAptoPrices(): bool
    {
        return !$this->aptoPrices->isEmpty();
    }

    /**
     * @return self
     */
    public function clearAptoPrices(): self
    {
        $this->aptoPrices->clear();
        return $this;
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return AptoPrice|null
     */
    protected function getAptoPriceObject(Currency $currency, AptoUuid $customerGroupId)
    {
        foreach ($this->aptoPrices as $aptoPrice) {
            /** @var AptoPrice $aptoPrice */
            if (
                $aptoPrice->getPrice()->getCurrency()->equals($currency) &&
                $aptoPrice->getCustomerGroupId()->getId() === $customerGroupId->getId()
            ) {
                return $aptoPrice;
            }
        }
        return null;
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @param AptoUuid $exceptForPriceId
     * @return bool
     * @throws AptoPriceDuplicateException
     */
    protected function assertNoAptoPriceDuplicates(Currency $currency, AptoUuid $customerGroupId, ?AptoUuid $productConditionId, AptoUuid $exceptForPriceId): bool
    {
        /** @var AptoPrice $aptoPrice */
        foreach ($this->aptoPrices as $aptoPrice) {
            if ($aptoPrice->getId()->getId() === $exceptForPriceId->getId()) {
                continue;
            }

            $productConditionSame = (!$productConditionId && !$aptoPrice->getProductConditionId()) ||
                ($productConditionId && $aptoPrice->getProductConditionId() && $productConditionId->getId() === $aptoPrice->getProductConditionId()->getId());

            if (
                $aptoPrice->getPrice()->getCurrency()->equals($currency) &&
                $aptoPrice->getCustomerGroupId()->getId() === $customerGroupId->getId() &&
                $productConditionSame
            ) {
                throw New AptoPriceDuplicateException('You cannot add a Price with the same Currency(' . $currency->getCode() . ') and the same CustomerGroup(' . $customerGroupId->getId() . ') twice.');
            }
        }
        return true;
    }

    /**
     * @param AptoUuid $priceId
     * @return bool
     */
    protected function containsAptoPriceId(AptoUuid $priceId): bool
    {
        return $this->aptoPrices->containsKey($priceId->getId());
    }

    /**
     * @param DomainEvent $event
     */
    private function publishAptoPriceEventIfCapable(DomainEvent $event)
    {
        if ($this instanceof AptoEventCapableEntity) {
            $this->publish($event);
        }
    }

    /**
     * @return AptoUuid
     * @throws InvalidUuidException
     */
    protected function nextAptoPriceId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @return Collection
     * @throws InvalidUuidException
     */
    protected function copyAptoPrices(): Collection
    {
        $collection = new ArrayCollection();

        /** @var AptoPrice $aptoPrice */
        foreach ($this->aptoPrices as $aptoPrice) {
            $id = $this->nextAptoPriceId();

            $collection->set(
                $id->getId(),
                $aptoPrice->copy($id)
            );
        }

        return $collection;
    }
}
