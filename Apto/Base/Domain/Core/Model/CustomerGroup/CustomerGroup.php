<?php

namespace Apto\Base\Domain\Core\Model\CustomerGroup;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;

class CustomerGroup extends AptoAggregate
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $inputGross;

    /**
     * @var boolean
     */
    protected $showGross;

    /**
     * @var AptoUuid
     */
    protected $shopId;

    /**
     * @var string|null
     */
    protected $externalId;

    /**
     * @var bool
     */
    protected $fallback;

    /**
     * CustomerGroup constructor.
     * @param AptoUuid $id
     * @param string $name
     * @param bool $inputGross
     * @param bool $showGross
     * @param AptoUuid $shopId
     * @param string|null $externalId
     */
    public function __construct(AptoUuid $id, string $name, bool $inputGross, bool $showGross, AptoUuid $shopId, string $externalId = null)
    {
        parent::__construct($id);
        $this->name = $name;
        $this->inputGross = $inputGross;
        $this->showGross = $showGross;
        $this->shopId = $shopId;
        $this->externalId = $externalId;
        $this->fallback = false;

        $this->publish(
            new CustomerGroupAdded(
                $id,
                $name,
                $inputGross,
                $showGross,
                $shopId,
                $externalId,
                $this->fallback
            )
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return CustomerGroup
     */
    public function setName(string $name): CustomerGroup
    {
        if ($this->name === $name) {
            return $this;
        }

        $this->name = $name;
        $this->publish(
            new CustomerGroupNameChanged(
                $this->getId(),
                $name
            )
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function getInputGross(): bool
    {
        return $this->inputGross;
    }

    /**
     * @param bool $inputGross
     * @return CustomerGroup
     */
    public function setInputGross(bool $inputGross): CustomerGroup
    {
        if ($this->inputGross === $inputGross) {
            return $this;
        }

        $this->inputGross = $inputGross;
        $this->publish(
            new CustomerGroupInputGrossChanged(
                $this->getId(),
                $inputGross
            )
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function getShowGross(): bool
    {
        return $this->showGross;
    }

    /**
     * @param bool $showGross
     * @return CustomerGroup
     */
    public function setShowGross(bool $showGross): CustomerGroup
    {
        if ($this->showGross === $showGross) {
            return $this;
        }

        $this->showGross = $showGross;
        $this->publish(
            new CustomerGroupShowGrossChanged(
                $this->getId(),
                $showGross
            )
        );

        return $this;
    }

    /**
     * @return AptoUuid
     */
    public function getShopId(): AptoUuid
    {
        return $this->shopId;
    }

    /**
     * @param AptoUuid $shopId
     * @return CustomerGroup
     */
    public function setShopId(AptoUuid $shopId): CustomerGroup
    {
        if ($this->shopId->getId() === $shopId->getId()) {
            return $this;
        }

        $this->shopId = $shopId;
        $this->publish(
            new CustomerGroupShopIdChanged(
                $this->getId(),
                $shopId
            )
        );

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return CustomerGroup
     */
    public function setExternalId(string $externalId = null): CustomerGroup
    {
        if ($this->externalId === $externalId) {
            return $this;
        }

        $this->externalId = $externalId;
        $this->publish(
            new CustomerGroupExternalIdChanged(
                $this->getId(),
                $externalId
            )
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function getFallback(): bool
    {
        return $this->fallback;
    }

    /**
     * @param bool $fallback
     * @return CustomerGroup
     */
    public function setFallback(bool $fallback): CustomerGroup
    {
        if ($this->fallback === $fallback) {
            return $this;
        }

        $this->fallback = $fallback;
        $this->publish(
            new CustomerGroupFallbackChanged(
                $this->getId(),
                $fallback
            )
        );

        return $this;
    }
}