<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Part;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPartsListCsv implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var array
     */
    private $state;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $productId;

    /**
     * @var string
     */
    private $customerGroupId;

    /**
     * @var string
     */
    private $currency;

    /**
     * @param string $productId
     * @param string $filename
     * @param array $state
     * @param string $locale
     * @param string $customerGroupId
     * @param string $currency
     */
    public function __construct(
        string $productId,
        string $filename,
        array $state,
        string $locale,
        string $currency,
        string $customerGroupId
    ) {
        $this->productId = $productId;
        $this->filename = $filename;
        $this->state = $state;
        $this->locale = $locale;
        $this->currency = $currency;
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getCustomerGroupId(): string
    {
        return $this->customerGroupId;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}

