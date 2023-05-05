<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Commands\SelectBoxItem;

use Apto\Base\Application\Core\CommandInterface;

class RemoveSelectBoxItemPrice implements CommandInterface
{
    /**
     * @var string
     */
    private $selectBoxItemId;

    /**
     * @var string
     */
    private $priceId;

    /**
     * RemoveSelectBoxItemPrice constructor.
     * @param string $selectBoxItemId
     * @param string $priceId
     */
    public function __construct(string $selectBoxItemId, string $priceId)
    {
        $this->selectBoxItemId = $selectBoxItemId;
        $this->priceId = $priceId;
    }

    /**
     * @return string
     */
    public function getSelectBoxItemId(): string
    {
        return $this->selectBoxItemId;
    }

    /**
     * @return string
     */
    public function getPriceId(): string
    {
        return $this->priceId;
    }
}