<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\CommandInterface;
use Apto\Base\Application\Core\Commands\AbstractUploadCommand;

class ImportPriceMatrix extends AbstractUploadCommand implements CommandInterface
{
    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $priceMatrixId;

    /**
     * @var string
     */
    protected $customerGroupId;

    /**
     * @var string
     */
    private $csvType;

    /**
     * ImportPriceMatrix constructor.
     * @param string $currency
     * @param string $priceMatrixId
     * @param string $customerGroupId
     * @param string $csvType
     */
    public function __construct(string $currency, string $priceMatrixId, string $customerGroupId, string $csvType = 'flat')
    {
        $this->currency = $currency;
        $this->priceMatrixId = $priceMatrixId;
        $this->customerGroupId = $customerGroupId;
        $this->csvType = $csvType;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
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
    public function getCsvType(): string
    {
        return $this->csvType;
    }
}
