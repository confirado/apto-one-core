<?php
namespace Apto\Catalog\Application\Frontend\Events\Configuration;

use Apto\Base\Application\Core\EventInterface;

class GuestConfigurationAdded implements EventInterface
{
    /**
     * @var string
     */
    private $configurationId;

    /**
     * @var string
     */
    private $customerEmail;

    /**
     * @var string
     */
    private $customerName;

    /**
     * @var array
     */
    private $payload;

    /**
     * GuestConfigurationAdded constructor.
     * @param string $configurationId
     * @param string $customerEmail
     * @param string $customerName
     * @param array $payload
     */
    public function __construct(string $configurationId, string $customerEmail, string $customerName, array $payload = [])
    {
        $this->configurationId = $configurationId;
        $this->customerEmail = $customerEmail;
        $this->customerName = $customerName;
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
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    /**
     * @return string
     */
    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
