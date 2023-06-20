<?php

namespace Apto\Base\Application\Backend\Commands\FrontendUser;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddFrontendUser implements CommandInterface
{
    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string|null
     */
    private $plainPassword;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $externalCustomerGroupId;

    /**
     * @var string
     */
    private $customerNumber;

    /**
     * @param bool $active
     * @param string $username
     * @param string|null $plainPassword
     * @param string $email
     * @param string $externalCustomerGroupId
     * @param string $customerNumber
     */
    public function __construct(bool $active, string $username, ?string $plainPassword, string $email, string $externalCustomerGroupId, string $customerNumber)
    {
        $this->active = $active;
        $this->username = strtolower($username);
        $this->plainPassword = $plainPassword;
        $this->email = strtolower($email);
        $this->externalCustomerGroupId = $externalCustomerGroupId;
        $this->customerNumber = $customerNumber;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
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
    public function getExternalCustomerGroupId(): string
    {
        return $this->externalCustomerGroupId;
    }

    /**
     * @return string
     */
    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }
}
