<?php

namespace Apto\Base\Application\Backend\Commands\CustomerGroup;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddCustomerGroup implements CommandInterface
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
     * @var string
     */
    protected $shopId;

    /**
     * @var null|string
     */
    protected $externalId;

    /**
     * @var bool
     */
    protected $fallback;

    /**
     * AddCustomerGroup constructor.
     * @param string $name
     * @param bool $inputGross
     * @param bool $showGross
     * @param string $shopId
     * @param string|null $externalId
     * @param bool $fallback
     */
    public function __construct(string $name, bool $inputGross, bool $showGross, string $shopId, string $externalId = null, bool $fallback)
    {
        $this->name = $name;
        $this->inputGross = $inputGross;
        $this->showGross = $showGross;
        $this->shopId = $shopId;
        $this->externalId = $externalId ? $externalId : null;
        $this->fallback = $fallback;
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
     * @return string
     */
    public function getShopId(): string
    {
        return $this->shopId;
    }

    /**
     * @return null|string
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