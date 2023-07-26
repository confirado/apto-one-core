<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

use Apto\Base\Application\Core\CommandInterface;

class UpdateRuleUsage implements CommandInterface
{
    /**
     * @var string
     */
    private $partId;

    /**
     * @var string
     */
    private $usageId;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @var int
     */
    private $active;

    /**
     * @var int
     */
    private $operator;

    /**
     * @var string
     */
    private $name;

    /**
     * UpdateRuleUsage constructor.
     * @param string $partId
     * @param string $usageId
     * @param string $quantity
     * @param int $active
     * @param string $name
     * @param int $operator
     */
    public function __construct(string $partId, string $usageId, string $quantity, int $active, string $name, int $operator)
    {
        $this->partId = $partId;
        $this->usageId = $usageId;
        $this->quantity = $quantity;
        $this->active = $active;
        $this->name = $name;
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getPartId(): string
    {
        return $this->partId;
    }

    /**
     * @return string
     */
    public function getUsageId(): string
    {
        return $this->usageId;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getOperator(): int
    {
        return $this->operator;
    }

    /**
     * @return int
     */
    public function getActive(): int
    {
        return $this->active;
    }
}

