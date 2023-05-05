<?php

namespace Apto\Catalog\Application\Core\Query\PriceMatrix;

use Apto\Base\Application\Core\PublicQueryInterface;

class GetPriceMatrixCsvString implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $priceMatrixId;

    /**
     * @var string
     */
    private $customerGroupId;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string
     */
    private $csvType;

    /**
     * GetPriceMatrixCsvString constructor.
     * @param string $priceMatrixId
     * @param string $customerGroupId
     * @param string $currencyCode
     * @param string $csvType
     */
    public function __construct(string $priceMatrixId, string $customerGroupId, string $currencyCode, string $csvType = 'flat')
    {
        $this->priceMatrixId = $priceMatrixId;
        $this->customerGroupId = $customerGroupId;
        $this->currencyCode = $currencyCode;
        $this->csvType = $csvType;
    }

    /**
     * @return string
     */
    public function getPriceMatrixId(): string
    {
        return $this->priceMatrixId;
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
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return string
     */
    public function getCsvType(): string
    {
        return $this->csvType;
    }
}
