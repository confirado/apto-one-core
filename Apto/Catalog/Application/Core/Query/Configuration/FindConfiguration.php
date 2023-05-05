<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class FindConfiguration implements PublicQueryInterface
{
    /**
     * @var string
     */
    protected $configurationId;

    /**
     * FindConfiguration constructor.
     * @param string $configurationId
     */
    public function __construct(string $configurationId)
    {
        $this->configurationId = $configurationId;
    }

    /**
     * @return string
     */
    public function getConfigurationId(): string
    {
        return $this->configurationId;
    }
}