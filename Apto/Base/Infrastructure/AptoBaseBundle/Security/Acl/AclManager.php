<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Security\Acl;

use Apto\Base\Application\Backend\Query\AclEntry\FindByShopRoleIdentity;
use Apto\Base\Domain\Backend\Model\Acl\AclIdentity;
use Apto\Base\Domain\Backend\Model\Acl\AclMask;
use Apto\Base\Domain\Backend\Model\Acl\AclMaskInvalidAttributeException;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryBus;
use Symfony\Component\Security\Core\Security;

class AclManager
{
    /**
     * @var QueryBus
     */
    private $queryBus;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param QueryBus $queryBus
     * @param Security $security
     */
    public function __construct(QueryBus $queryBus, Security $security)
    {
        // @todo: implement acl caching by providing an acl cache, e.g. memcached, inline php etc.
        $this->queryBus = $queryBus;
        $this->security = $security;
    }

    /**
     * @param string|null $shopId
     * @param AclIdentity $identity
     * @param AclMask $mask
     * @return bool
     * @throws AclMaskInvalidAttributeException
     */
    public function isGranted(?string $shopId, AclIdentity $identity, AclMask $mask): bool
    {
        // @todo: check if acl cache entry exists

        // get curren token
        if (null === $this->security->getUser()) {
            return false;
        }

        // get direct reachable roles
        $directRoles = $this->security->getUser()->getRoles();

        // bypass role ROLE_SUPER_ADMIN
        foreach ($directRoles as $directRole) {
            if($directRole == 'ROLE_SUPER_ADMIN') {
                return true;
            }
        }

        // get all reachable roles
        foreach ($directRoles as $role) {
            // if access is granted by specific ace, stop here
            if ($this->matchAces($role, $shopId, $identity, $mask)) {
                // @todo: set acl cache entry
                return true;
            }
        }

        // if no explicit ace has been found, no access is granted
        return false;
    }

    /**
     * @param string $role
     * @param string|null $shopId
     * @param AclIdentity $identity
     * @param AclMask $mask
     * @return bool
     * @throws AclMaskInvalidAttributeException
     */
    protected function matchAces(string $role, ?string $shopId, AclIdentity $identity, AclMask $mask): bool
    {
        $acesQuery = new FindByShopRoleIdentity(
            $role,
            $identity->getModelClass(),
            $shopId,
            $identity->getEntityId(),
            $identity->getFieldName()
        );

        $aces = null;
        $this->queryBus->handle($acesQuery, $aces);

        $combinedMask = 0;

        foreach ($aces['data'] as $ace) {
            $combinedMask |= $ace['mask'];
        }

        return $mask->matches(new AclMask($combinedMask));
    }

    /**
     * @return boolean
     */
    public function clearCache(): bool
    {
        // @todo: clear acl cache
        return false;
    }
}