<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

class AddOfferConfiguration extends ConfigurationCommand
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $payload;

    /**
     * @param string $productId
     * @param array $state
     * @param string $email
     * @param string $name
     * @param array $payload
     */
    public function __construct(string $productId, array $state, string $email, string $name, array $payload = [])
    {
        parent::__construct($productId, $state);
        $this->email = $email;
        $this->name = $name;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
