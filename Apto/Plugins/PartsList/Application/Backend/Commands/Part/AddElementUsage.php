<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

class AddElementUsage extends AddUsage
{
    /**
     * @var string
     */
    private $value;

    /**
     * AddElementUsage constructor.
     * @param string $partId
     * @param string $usageForUuid
     * @param string $quantity
     * @param string $value
     */
    public function __construct(string $partId, string $usageForUuid, string $quantity, string $value, string $productId)
    {
        parent::__construct($partId, $usageForUuid, $quantity, $productId);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
