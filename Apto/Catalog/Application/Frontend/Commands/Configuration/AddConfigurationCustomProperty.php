<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

use Apto\Base\Application\Core\PublicCommandInterface;

class AddConfigurationCustomProperty implements PublicCommandInterface
{
    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string
     */
    private $configurationType;

    /**
     * @var string
     */
    private $customerPropertyKey;

    /**
     * @var string
     */
    private $customerPropertyValue;

    /**
     * @var bool
     */
    private $translatable;


    /**
     * AddConfigurationCustomProperty constructor.
     * @param string $id
     * @param string $configurationType
     * @param string $customerPropertyKey
     * @param string $customerPropertyValue
     * @param bool $translatable
     */
    public function __construct(string $id, string $configurationType, string $customerPropertyKey, string $customerPropertyValue, bool $translatable)
    {
        $this->id = $id;
        $this->configurationType = $configurationType;
        $this->customerPropertyKey = $customerPropertyKey;
        $this->customerPropertyValue = $customerPropertyValue;
        $this->translatable = $translatable;
    }

    /**
     * @return string
     */
    public function getConfigurationType(): string
    {
        return $this->configurationType;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCustomerPropertyKey(): string
    {
        return $this->customerPropertyKey;
    }

    /**
     * @return string
     */
    public function getCustomerPropertyValue(): string
    {
        return $this->customerPropertyValue;
    }

    /**
     * @return bool
     */
    public function isTranslatable(): bool
    {
        return $this->translatable;
    }
}
