<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

class AddProductUsage extends AddUsage
{
    /**
     * AddProductUsage constructor.
     * @param string $partId
     * @param string $usageForUuid
     * @param string $quantity
     */
    public function __construct(string $partId, string $usageForUuid, string $quantity)
    {
        parent::__construct($partId, $usageForUuid, $quantity, $usageForUuid);
    }
}