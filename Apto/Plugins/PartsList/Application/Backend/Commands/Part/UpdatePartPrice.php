<?php
namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

class UpdatePartPrice extends AddPartPriceAbstract
{
    /**
     * @var string
     */
    private $priceId;

    /**
     * UpdatePartPrice constructor.
     * @param string $id
     * @param string $priceId
     * @param $amount
     * @param string $currency
     * @param string $customerGroupId
     */
    public function __construct(string $id, string $priceId, $amount, string $currency, string $customerGroupId)
    {
        parent::__construct($id, $amount, $currency, $customerGroupId);
        $this->priceId = $priceId;
    }

    /**
     * @return string
     */
    public function getPriceId(): string
    {
        return $this->priceId;
    }
}