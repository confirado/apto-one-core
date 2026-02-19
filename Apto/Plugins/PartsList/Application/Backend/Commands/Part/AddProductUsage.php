<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

class AddProductUsage extends AddUsage
{
    /**
     * AddProductUsage constructor.
     * @param string $partId
     * @param string $usageForUuid
     * @param string $quantity
     * @param string $value
     */
    public function __construct(string $partId, string $usageForUuid, string $quantity, string $value)
    {
        parent::__construct($partId, $usageForUuid, $quantity, $value, $usageForUuid);
    }
}
