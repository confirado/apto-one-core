<?php

namespace Apto\Catalog\Application\Frontend\Query\Configuration;

use Apto\Base\Application\Core\PublicQueryInterface;

class GetConfigurationState implements PublicQueryInterface
{
    /**
     * @var string
     */
    private string $productId;

    /**
     * @var array
     */
    private array $state;

    /**
     * @var array e.g. [
     *   'init' => true,
     *   'set' => [
     *     ['sectionId' => '123123-123123132-123123', 'elementId' => '123123-123123132-123123', 'property' => 'width', 'value' => 123],
     *     ['sectionId' => '123123-123123132-123123', 'elementId' => '123123-123123132-123123', 'property' => 'height', 'value' => 321],
     *     ['sectionId' => '123123-123123132-123123', 'elementId' => '123123-123123132-123123', 'value' => null]
     *     ['sectionId' => '123123-123123132-123123', 'elementId' => '123123-123123132-123123']
     *   ],
     *   'remove' => [
     *     ['sectionId' => '123123-123123132-123123', 'elementId' => '123123-123123132-123123'],
     *     ['sectionId' => '123123-123123132-123123', 'elementId' => null],
     *     ['sectionId' => '123123-123123132-123123'],
     *   ],
     *   'complete' => [
     *     ['sectionId' => '123123-123123132-123123', 'complete' => false],
     *     ['sectionId' => '123123-123123132-123123', 'complete' => true],
     *     // without complete flag section will set to complete true
     *     ['sectionId' => '123123-123123132-123123']
     *   ],
     *   'repair' => [
     *     'maxTries' => 3,
     *     'operators' => [1, 3]
     *   ]
     * ]
     */
    private ?array $intention;

    /**
     * @var array|null
     */
    private ?array $connector;

    /**
     * @var array|null
     */
    private ?array $user;

    /**
     * @param string $productId
     * @param array $state
     * @param array|null $intention
     */
    public function __construct(string $productId, array $state, ?array $intention = null, ?array $connector = null, ?array $user = null)
    {
        $this->productId = $productId;
        $this->state = $state;
        $this->intention = $intention ?: [];
        $this->connector = $connector;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @return array
     */
    public function getIntention(): array
    {
        return $this->intention;
    }

    /**
     * @return array|null
     */
    public function getConnector(): ?array
    {
        return $this->connector;
    }

    /**
     * @return array|null
     */
    public function getUser(): ?array
    {
        return $this->user;
    }
}
