<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

class AddGuestConfiguration extends ConfigurationCommand
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
     * @var bool
     */
    private $sendMail;

    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $payload;

    /**
     * AddGuestConfiguration constructor.
     * @param string $productId
     * @param array $state
     * @param string $email
     * @param string $name
     * @param bool $sendMail
     * @param string $id
     * @param array $payload
     */
    public function __construct(string $productId, array $state, string $email, string $name, bool $sendMail = true, string $id = '', array $payload = [])
    {
        parent::__construct($productId, $state);
        $this->email = $email;
        $this->name = $name;
        $this->sendMail = $sendMail;
        $this->id = $id;
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
     * @return bool
     */
    public function getSendMail(): bool
    {
        return $this->sendMail;
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
