<?php

namespace Apto\Base\Domain\Backend\Model\User;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class UserApiOriginUpdated extends AbstractDomainEvent
{
    /**
     * @var string|null
     */
    private $apiOrigin;

    /**
     * UserApiOriginUpdated constructor.
     * @param AptoUuid $id
     * @param string|null $apiOrigin
     */
    public function __construct(AptoUuid $id, $apiOrigin)
    {
        parent::__construct($id);
        $this->apiOrigin = $apiOrigin;
    }

    /**
     * @return string|null
     */
    public function getApiOrigin()
    {
        return $this->apiOrigin;
    }
}