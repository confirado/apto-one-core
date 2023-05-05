<?php

namespace Apto\Base\Application\Backend\Commands\FrontendUser;

class UpdateFrontendUser extends AbstractAddFrontendUser
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdateFrontendUser constructor.
     * @param string $id
     * @param bool $active
     * @param string $username
     * @param $plainPassword
     * @param string $email
     * @param string $externalCustomerGroupId
     */
    public function __construct(string $id, bool $active, string $username, $plainPassword, string $email, string $externalCustomerGroupId)
    {
        parent::__construct($active, $username, $plainPassword, $email, $externalCustomerGroupId);
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
