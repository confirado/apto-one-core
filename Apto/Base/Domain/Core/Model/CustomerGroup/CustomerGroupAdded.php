<?php

namespace Apto\Base\Domain\Core\Model\CustomerGroup;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class CustomerGroupAdded extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $inputGross;

    /**
     * @var bool
     */
    private $showGross;

    /**
     * @var AptoUuid
     */
    private $shopId;

    /**
     * @var string|null
     */
    private $externalId;

    /**
     * @var bool
     */
    private $fallback;

    /**
     * CustomerGroupAdded constructor.
     * @param AptoUuid $id
     * @param string $name
     * @param bool $inputGross
     * @param bool $showGross
     * @param AptoUuid $shopId
     * @param string|null $externalId
     * @param bool $fallback
     */
    public function __construct(AptoUuid $id, string $name, bool $inputGross, bool $showGross, AptoUuid $shopId, string $externalId = null, bool $fallback)
    {
        parent::__construct($id);
        $this->name = $name;
        $this->inputGross = $inputGross;
        $this->showGross = $showGross;
        $this->shopId = $shopId;
        $this->externalId = $externalId;
        $this->fallback= $fallback;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getInputGross(): bool
    {
        return $this->inputGross;
    }

    /**
     * @return bool
     */
    public function getShowGross(): bool
    {
        return $this->showGross;
    }

    /**
     * @return AptoUuid
     */
    public function getShopId(): AptoUuid
    {
        return $this->shopId;
    }

    /**
     * @return string|null
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @return bool
     */
    public function getFallback(): bool
    {
        return $this->fallback;
    }
}