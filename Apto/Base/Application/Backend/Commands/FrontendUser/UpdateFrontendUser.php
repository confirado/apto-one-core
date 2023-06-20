<?php

namespace Apto\Base\Application\Backend\Commands\FrontendUser;

class UpdateFrontendUser extends AbstractAddFrontendUser
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     * @param bool $active
     * @param string $username
     * @param string|null $plainPassword
     * @param string $email
     * @param string $externalCustomerGroupId
     * @param string $customerNumber
     */
    public function __construct(string $id, bool $active, string $username, ?string $plainPassword, string $email, string $externalCustomerGroupId, string $customerNumber)
    {
        parent::__construct($active, $username, $plainPassword, $email, $externalCustomerGroupId, $customerNumber);
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
