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
     * @var string
     */
    private $value;

    /**
     * AddRuleUsage constructor.
     * @param string $partId
     * @param string $name
     * @param string $quantity
     * @param string $value
     */
    public function __construct(string $partId, string $name, string $quantity, string $value)
    {
        $this->partId = $partId;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->value = $value;
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

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
