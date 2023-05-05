<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus;

use Apto\Base\Application\Core\PublicCommandInterface;
use Apto\Base\Application\Core\PublicQueryInterface;
use Apto\Base\Domain\Backend\Model\Acl\AclIdentity;
use Apto\Base\Domain\Backend\Model\Acl\AclMask;
use Apto\Base\Infrastructure\AptoBaseBundle\Security\Acl\AclManager;
use Apto\Base\Domain\Backend\Model\Acl\AclMaskInvalidAttributeException;

abstract class DefaultMessageBusRule implements MessageBusFirewallRule
{
    /**
     * @var AclManager
     */
    protected $aclManager;

    /**
     * @param AclManager $aclManager
     */
    public function __construct(AclManager $aclManager)
    {
        $this->aclManager = $aclManager;
    }

    /**
     * @param $message
     * @return bool
     * @throws AclMaskInvalidAttributeException
     */
    public function isExecutionAllowed($message): bool
    {
        if ($message instanceof PublicCommandInterface || $message instanceof PublicQueryInterface) {
            return true;
        }
        return $this->isGranted(get_class($message));
    }

    /**
     * @param string $messageClass
     * @return bool
     * @throws AclMaskInvalidAttributeException
     */
    public function isGranted(string $messageClass): bool
    {
        return $this->aclManager->isGranted(null, new AclIdentity($messageClass, null, null), new AclMask(AclMask::EXECUTE));
    }
}