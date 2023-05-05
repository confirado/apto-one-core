<?php

namespace Apto\Base\Domain\Backend\Model\User;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class UserApiKeyUpdated extends AbstractDomainEvent
{
    /**
     * @var string|null
     */
    private $apiKey;

    /**
     * UserApiKeyUpdated constructor.
     * @param AptoUuid $id
     * @param string|null $apiKey
     */
    public function __construct(AptoUuid $id, $apiKey)
    {
        parent::__construct($id);
        $this->apiKey = $apiKey;
    }

    /**
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}