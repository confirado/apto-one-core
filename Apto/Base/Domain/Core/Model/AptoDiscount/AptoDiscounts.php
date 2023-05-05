<?php

namespace Apto\Base\Domain\Core\Model\AptoDiscount;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;

/**
 * Trait AptoDiscounts
 * @package Apto\Base\Domain\Core\Model\AptoDiscount
 */
trait AptoDiscounts
{
    /**
     * @var Collection
     */
    protected $aptoDiscounts;

    /**
     * @param float $discount
     * @param AptoUuid $customerGroupId
     * @param AptoTranslatedValue $name
     * @return self
     * @throws AptoDiscountDuplicateException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function addAptoDiscount(float $discount, AptoUuid $customerGroupId, AptoTranslatedValue $name): self
    {
        $discountId = $this->nextAptoDiscountId();
        if ($this->containsAptoDiscountId($discountId)) {
            return $this;
        }

        $this->assertNoAptoDiscountDuplicates($customerGroupId, $discountId);

        $this->aptoDiscounts->set(
            $discountId->getId(),
            new AptoDiscount(
                $discountId,
                $discount,
                $customerGroupId,
                $name
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $discountId
     * @return self
     */
    public function removeAptoDiscount(AptoUuid $discountId): self
    {
        if (!$this->containsAptoDiscountId($discountId)) {
            return $this;
        }

        $this->aptoDiscounts->remove($discountId->getId());
        return $this;
    }

    /**
     * @param AptoUuid $discountId
     * @param float $discount
     * @return self
     */
    public function setAptoDiscountValue(AptoUuid $discountId, float $discount): self
    {
        if (!$this->containsAptoDiscountId($discountId)) {
            return $this;
        }

        /** @var AptoDiscount $aptoDiscount */
        $aptoDiscount = $this->aptoDiscounts->get($discountId->getId());

        if ($aptoDiscount->getDiscount() !== $discount) {
            $aptoDiscount->setDiscount($discount);
        }
        return $this;
    }

    /**
     * @param AptoUuid $discountId
     * @param AptoUuid $customerGroupId
     * @return self
     * @throws AptoDiscountDuplicateException
     */
    public function setAptoDiscountCustomerGroupId(AptoUuid $discountId, AptoUuid $customerGroupId): self
    {
        if (!$this->containsAptoDiscountId($discountId)) {
            return $this;
        }

        /** @var AptoDiscount $aptoDiscount */
        $aptoDiscount = $this->aptoDiscounts->get($discountId->getId());

        if ($aptoDiscount->getCustomerGroupId()->getId() !== $customerGroupId->getId()) {
            $this->assertNoAptoDiscountDuplicates($customerGroupId, $discountId);

            $aptoDiscount->setCustomerGroupId($customerGroupId);
        }
        return $this;
    }

    /**
     * @param AptoUuid $discountId
     * @param AptoTranslatedValue $name
     * @return self
     */
    public function setAptoDiscountName(AptoUuid $discountId, AptoTranslatedValue $name): self
    {
        if (!$this->containsAptoDiscountId($discountId)) {
            return $this;
        }

        /** @var AptoDiscount $aptoDiscount */
        $aptoDiscount = $this->aptoDiscounts->get($discountId->getId());

        if (!$aptoDiscount->getName()->equals($name)) {
            $aptoDiscount->setName($name);
        }
        return $this;
    }

    /**
     * @param AptoUuid $discountId
     * @return AptoTranslatedValue|null
     */
    public function getAptoDiscountName(AptoUuid $discountId): ?AptoTranslatedValue
    {
        if (!$this->containsAptoDiscountId($discountId)) {
            return null;
        }

        /** @var AptoDiscount $aptoDiscount */
        $aptoDiscount = $this->aptoDiscounts->get($discountId->getId());
        return $aptoDiscount->getName();
    }

    /**
     * @param AptoUuid $discountId
     * @param AptoTranslatedValue $description
     * @return self
     */
    public function setAptoDiscountDescription(AptoUuid $discountId, AptoTranslatedValue $description): self
    {
        if (!$this->containsAptoDiscountId($discountId)) {
            return $this;
        }

        /** @var AptoDiscount $aptoDiscount */
        $aptoDiscount = $this->aptoDiscounts->get($discountId->getId());

        if (!$aptoDiscount->getDescription()->equals($description)) {
            $aptoDiscount->setDescription($description);
        }
        return $this;
    }

    /**
     * @param AptoUuid $discountId
     * @return AptoTranslatedValue|null
     */
    public function getAptoDiscountDescription(AptoUuid $discountId): ?AptoTranslatedValue
    {
        if (!$this->containsAptoDiscountId($discountId)) {
            return null;
        }

        /** @var AptoDiscount $aptoDiscount */
        $aptoDiscount = $this->aptoDiscounts->get($discountId->getId());
        return $aptoDiscount->getDescription();
    }

    /**
     * @return self
     */
    public function clearAptoDiscounts(): self
    {
        $this->aptoDiscounts->clear();
        return $this;
    }

    /**
     * @param AptoUuid $customerGroupId
     * @param AptoUuid $exceptForDiscountId
     * @return bool
     * @throws AptoDiscountDuplicateException
     */
    protected function assertNoAptoDiscountDuplicates(AptoUuid $customerGroupId, AptoUuid $exceptForDiscountId)
    {
        /** @var AptoDiscount $aptoDiscount */
        foreach ($this->aptoDiscounts as $aptoDiscount) {
            if ($aptoDiscount->getId()->getId() === $exceptForDiscountId->getId()) {
                continue;
            }

            if (
                $aptoDiscount->getCustomerGroupId()->getId() === $customerGroupId->getId()
            ) {
                throw New AptoDiscountDuplicateException('You cannot add a Discount with the same CustomerGroup(' . $customerGroupId->getId() . ') twice.');
            }
        }
        return true;
    }

    /**
     * @param AptoUuid $discountId
     * @return bool
     */
    protected function containsAptoDiscountId(AptoUuid $discountId): bool
    {
        return $this->aptoDiscounts->containsKey($discountId->getId());
    }

    /**
     * @return AptoUuid
     * @throws InvalidUuidException
     */
    protected function nextAptoDiscountId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @return Collection
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    protected function copyAptoDiscounts(): Collection
    {
        $collection = new ArrayCollection();

        /** @var AptoDiscount $aptoDiscount */
        foreach ($this->aptoDiscounts as $aptoDiscount) {
            $id = $this->nextAptoDiscountId();

            $collection->set(
                $id->getId(),
                $aptoDiscount->copy($id)
            );
        }

        return $collection;
    }
}