<?php

namespace Apto\Base\Domain\Core\Model\AptoPriceFormula;

use Apto\Base\Domain\Core\Model\AptoEventCapableEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Money\Currency;

/**
 * Trait AptoPrices
 * @package Apto\Base\Domain\Core\Model\AptoPriceFormula
 */
trait AptoPriceFormulas
{
    /**
     * @var Collection
     */
    protected $aptoPriceFormulas;

    /**
     * @param string $formula
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return self
     * @throws InvalidUuidException
     * @throws AptoPriceFormulaDuplicateException
     */
    public function addAptoPriceFormula(string $formula, Currency $currency, AptoUuid $customerGroupId): self
    {
        $priceFormulaId = $this->nextAptoPriceFormulaId();
        if ($this->containsAptoPriceFormulaId($priceFormulaId)) {
            return $this;
        }

        $this->assertNoAptoPriceFormulaDuplicates($currency, $customerGroupId, $priceFormulaId);

        $this->aptoPriceFormulas->set(
            $priceFormulaId->getId(),
            new AptoPriceFormula(
                $priceFormulaId,
                $formula,
                $currency,
                $customerGroupId
            )
        );

        $this->publishAptoPriceFormulaEventIfCapable(
            new AptoPriceFormulaAdded(
                $priceFormulaId,
                $this->getId(),
                $formula,
                $currency,
                $customerGroupId
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $priceFormulaId
     * @return self
     */
    public function removeAptoPriceFormula(AptoUuid $priceFormulaId): self
    {
        if (!$this->containsAptoPriceFormulaId($priceFormulaId)) {
            return $this;
        }

        $this->aptoPriceFormulas->remove($priceFormulaId->getId());
        $this->publishAptoPriceFormulaEventIfCapable(
            new AptoPriceFormulaRemoved(
                $priceFormulaId,
                $this->getId()
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $priceFormulaId
     * @param Currency $currency
     * @return self
     * @throws AptoPriceFormulaDuplicateException
     */
    public function setAptoPriceFormulaCurrency(AptoUuid $priceFormulaId, Currency $currency): self
    {
        if (!$this->containsAptoPriceFormulaId($priceFormulaId)) {
            return $this;
        }

        /** @var AptoPriceFormula $aptoPriceFormula */
        $aptoPriceFormula = $this->aptoPriceFormulas->get($priceFormulaId->getId());

        if (!$aptoPriceFormula->getCurrency()->equals($currency)) {
            $this->assertNoAptoPriceFormulaDuplicates($currency, $aptoPriceFormula->getCustomerGroupId(), $priceFormulaId);

            $aptoPriceFormula->setCurrency($currency);
            $this->publishAptoPriceFormulaEventIfCapable(
                new AptoPriceFormulaCurrencyChanged(
                    $priceFormulaId,
                    $this->getId(),
                    $aptoPriceFormula->getCurrency()
                )
            );
        }

        return $this;
    }

    /**
     * @param AptoUuid $priceFormulaId
     * @param AptoUuid $customerGroupId
     * @return self
     * @throws AptoPriceFormulaDuplicateException
     */
    public function setAptoPriceFormulaCustomerGroupId(AptoUuid $priceFormulaId, AptoUuid $customerGroupId): self
    {
        if (!$this->containsAptoPriceFormulaId($priceFormulaId)) {
            return $this;
        }

        /** @var AptoPriceFormula $aptoPriceFormula */
        $aptoPriceFormula = $this->aptoPriceFormulas->get($priceFormulaId->getId());

        if ($aptoPriceFormula->getCustomerGroupId()->getId() !== $customerGroupId->getId()) {
            $this->assertNoAptoPriceFormulaDuplicates($aptoPriceFormula->getCurrency(), $customerGroupId, $priceFormulaId);

            $aptoPriceFormula->setCustomerGroupId($customerGroupId);

            $this->publishAptoPriceFormulaEventIfCapable(
                new AptoPriceFormulaCustomerGroupIdChanged(
                    $priceFormulaId,
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
     * @return string|null
     */
    public function getAptoPriceFormula(Currency $currency, AptoUuid $customerGroupId): ?string
    {
        $aptoPriceFormula = $this->getAptoPriceFormulaObject($currency, $customerGroupId);
        return null !== $aptoPriceFormula ? $aptoPriceFormula->getFormula() : null;
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return AptoUuid|null
     */
    public function getAptoPriceFormulaId(Currency $currency, AptoUuid $customerGroupId): ?AptoUuid
    {
        $aptoPrice = $this->getAptoPriceFormulaObject($currency, $customerGroupId);
        return null !== $aptoPrice ? $aptoPrice->getId() : null;
    }

    /**
     * @return bool
     */
    public function hasAptoPriceFormulas(): bool
    {
        return !$this->aptoPriceFormulas->isEmpty();
    }

    /**
     * @return self
     */
    public function clearAptoPriceFormulas(): self
    {
        $this->aptoPriceFormulas->clear();
        return $this;
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return AptoPriceFormula|null
     */
    protected function getAptoPriceFormulaObject(Currency $currency, AptoUuid $customerGroupId): ?AptoPriceFormula
    {
        foreach ($this->aptoPriceFormulas as $aptoPriceFormula) {
            /** @var AptoPriceFormula $aptoPriceFormula */
            if (
                $aptoPriceFormula->getCurrency()->equals($currency) &&
                $aptoPriceFormula->getCustomerGroupId()->getId() === $customerGroupId->getId()
            ) {
                return $aptoPriceFormula;
            }
        }
        return null;
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @param AptoUuid $exceptForPriceFormulaId
     * @return bool
     * @throws AptoPriceFormulaDuplicateException
     */
    protected function assertNoAptoPriceFormulaDuplicates(Currency $currency, AptoUuid $customerGroupId, AptoUuid $exceptForPriceFormulaId): bool
    {
        /** @var AptoPriceFormula $aptoPriceFormula */
        foreach ($this->aptoPriceFormulas as $aptoPriceFormula) {
            if ($aptoPriceFormula->getId()->getId() === $exceptForPriceFormulaId->getId()) {
                continue;
            }

            if (
                $aptoPriceFormula->getCurrency()->equals($currency) &&
                $aptoPriceFormula->getCustomerGroupId()->getId() === $customerGroupId->getId()
            ) {
                throw New AptoPriceFormulaDuplicateException(sprintf(
                    'You cannot add a PriceFormula with the same Currency(%s) and the same CustomerGroup(%s) twice.',
                    $currency->getCode(),
                    $customerGroupId->getId()
                ));
            }
        }
        return true;
    }

    /**
     * @param AptoUuid $priceFormulaId
     * @return bool
     */
    protected function containsAptoPriceFormulaId(AptoUuid $priceFormulaId): bool
    {
        return $this->aptoPriceFormulas->containsKey($priceFormulaId->getId());
    }

    /**
     * @param DomainEvent $event
     */
    private function publishAptoPriceFormulaEventIfCapable(DomainEvent $event)
    {
        if ($this instanceof AptoEventCapableEntity) {
            $this->publish($event);
        }
    }

    /**
     * @return AptoUuid
     * @throws InvalidUuidException
     */
    protected function nextAptoPriceFormulaId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @return Collection
     * @throws InvalidUuidException
     */
    protected function copyAptoPriceFormulas(): Collection
    {
        $collection = new ArrayCollection();

        /** @var AptoPriceFormula $aptoPriceFormula */
        foreach ($this->aptoPriceFormulas as $aptoPriceFormula) {
            $id = $this->nextAptoPriceId();

            $collection->set(
                $id->getId(),
                $aptoPriceFormula->copy($id)
            );
        }

        return $collection;
    }
}
