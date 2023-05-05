<?php

namespace Apto\Base\Application\Core\Query\Customer;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCustomerByShopAndExternalId implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $shopId;

    /**
     * @var string
     */
    private $externalId;

    /**
     * FindCustomerByShopAndExternalId constructor.
     * @param string $shopId
     * @param string $externalId
     */
    public function __construct(string $shopId, string $externalId)
    {
        $this->shopId = $shopId;
        $this->externalId = $externalId;
    }

    /**
     * @return string
     */
    public function getShopId(): string
    {
        return $this->shopId;
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }
}