<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

use Apto\Base\Application\Core\CommandInterface;

class AddRuleUsage implements CommandInterface
{
    /**
     * @var string
     */
    private $partId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $quantity;

    /**
     * AddRuleUsage constructor.
     * @param string $partId
     * @param string $name
     * @param string $quantity
     */
    public function __construct(string $partId, string $name, string $quantity)
    {
        $this->partId = $partId;
        $this->name = $name;
        $this->quantity = $quantity;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->quantity;
    }
}
