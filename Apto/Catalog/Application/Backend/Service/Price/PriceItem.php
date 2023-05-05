<?php

namespace Apto\Catalog\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Money\Currency;
use Money\Money;

class PriceItem
{
    /**
     * @var AptoUuid
     */
    private $aptoPriceId;

    /**
     * @var Money
     */
    private $money;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $priceType;

    /**
     * @var AptoUuid
     */
    private $customerGroup;

    /**
     * @var AptoUuid
     */
    private $entityId;

    /**
     * PriceItem constructor.
     * @param string $priceType
     * @param string $fieldName
     * @param AptoUuid $aptoPriceId
     * @param Money $money
     * @param AptoUuid $customerGroup
     * @param AptoUuid $entityId
     */
    public function __construct(string $priceType, string $fieldName, AptoUuid $aptoPriceId, Money $money, AptoUuid $customerGroup, AptoUuid $entityId)
    {
        $this->priceType = $priceType;
        $this->fieldName = $fieldName;
        $this->aptoPriceId = $aptoPriceId;
        $this->money = $money;
        $this->customerGroup = $customerGroup;
        $this->entityId = $entityId;
    }

    /**
     * @return AptoUuid
     */
    public function getAptoPriceId(): AptoUuid
    {
        return $this->aptoPriceId;
    }

    /**
     * @return Money
     */
    public function getMoney(): Money
    {
        return $this->money;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @return string
     */
    public function getPriceType(): string
    {
        return $this->priceType;
    }

    /**
     * @return AptoUuid
     */
    public function getCustomerGroup(): AptoUuid
    {
        return $this->customerGroup;
    }

    /**
     * @return AptoUuid
     */
    public function getEntityId(): AptoUuid
    {
        return $this->entityId;
    }

    /**
     * @param array $array
     * @param float $multiplier
     * @return PriceItem
     * @throws InvalidUuidException
     */
    public static function fromArray(array $array, float $multiplier = 1): PriceItem
    {
        $money = new Money($array['money']['amount'], new Currency($array['money']['currency']));
        return new PriceItem(
            $array['priceType'],
            $array['fieldName'],
            new AptoUuid($array['aptoPriceId']),
            $money->multiply($multiplier),
            new AptoUuid($array['customerGroup']),
            new AptoUuid($array['entityId'])
        );
    }
}
