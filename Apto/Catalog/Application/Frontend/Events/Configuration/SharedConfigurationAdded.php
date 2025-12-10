<?php
namespace Apto\Catalog\Application\Frontend\Events\Configuration;

use Apto\Base\Application\Core\EventInterface;

class SharedConfigurationAdded implements EventInterface
{
    /**
     * @var string
     */
    private $configurationId;

    /**
     * @var array
     */
    private $payload;

    /**
     * SharedConfigurationAdded constructor.
     * @param string $configurationId
     * @param array $payload
     */
    public function __construct(string $configurationId, array $payload = [])
    {
        $this->configurationId = $configurationId;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getConfigurationId(): string
    {
        return $this->configurationId;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
