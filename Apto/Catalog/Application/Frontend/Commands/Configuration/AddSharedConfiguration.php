<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

class AddSharedConfiguration extends ConfigurationCommand
{
    /**
     * @var string
     */
    private string $productId;

    /**
     * @var array
     */
    private $state;

    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $payload;

    /**
     * AddSharedConfiguration constructor.
     * @param string $productId
     * @param array $state
     * @param string $id
     * @param array $payload
     */
    public function __construct(string $productId, array $state, string $id = '', array $payload = [])
    {
        $this->productId = $productId;
        $this->state = $state;
        $this->id = $id;
        $this->payload = $payload;
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
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
