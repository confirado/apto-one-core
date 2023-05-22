<?php

namespace Apto\Base\Application\Backend\Commands\AclEntry;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Backend\Model\Acl\AclEntry;
use Apto\Base\Domain\Backend\Model\Acl\AclEntryRepository;
use Apto\Base\Domain\Backend\Model\Acl\AclIdentity;
use Apto\Base\Domain\Backend\Model\Acl\AclMask;
use Apto\Base\Domain\Backend\Model\Acl\AclMaskBuilder;
use Apto\Base\Domain\Backend\Model\UserRole\UserRoleIdentifier;
use Apto\Base\Domain\Backend\Model\UserRole\UserRoleRepository;

class AclEntryHandler implements CommandHandlerInterface
{
    /**
     * @var AclEntryRepository
     */
    private $aclEntryRepository;

    /**
     * @param AclEntryRepository $aclEntryRepository
     */
    public function __construct(AclEntryRepository $aclEntryRepository)
    {
        $this->aclEntryRepository = $aclEntryRepository;
    }

    /**
     * @param AclPermission $command
     */
    public function handleAddAclPermission(AclPermission $command)
    {
        $aclMask = $this->getAclMask($command);
        $aclIdentity = $this->getAclIdentity($command);

        $aclEntry = $this->aclEntryRepository->findByShopRoleIdentity(null, $command->getRoleId(), $aclIdentity);
        if (null !== $aclEntry) {
            // if mask to add is not in existing mask, add it
            if (!$aclEntry->getMask()->matchedBy($aclMask)) {
                $aclMask = AclMaskBuilder::addMasks($aclEntry->getMask(), $aclMask);
                $aclEntry->setMask($aclMask);
                $this->aclEntryRepository->update($aclEntry);
            }
        } else {
            $aclEntry = new AclEntry(
                null,
                new UserRoleIdentifier($command->getRoleId()),
                $aclIdentity,
                $aclMask
            );

            $this->aclEntryRepository->add($aclEntry);
        }
    }

    /**
     * @param AclPermission $command
     */
    public function handleRemoveAclPermission(AclPermission $command)
    {
        $aclMask = $this->getAclMask($command);
        $aclIdentity = $this->getAclIdentity($command);

        $aclEntry = $this->aclEntryRepository->findByShopRoleIdentity(null, $command->getRoleId(), $aclIdentity);
        if (null !== $aclEntry) {
            // if mask to remove is in existing mask, subtract it
            if ($aclEntry->getMask()->matchedBy($aclMask)) {
                $aclMask = AclMaskBuilder::subtractMasks($aclEntry->getMask(), $aclMask);
                $aclEntry->setMask($aclMask);

                if (!$aclEntry->getMask()->isNone()) {
                    $this->aclEntryRepository->update($aclEntry);
                }
            }

            if ($aclEntry->getMask()->isNone()) {
                $this->aclEntryRepository->remove($aclEntry);
            }
        }
    }

    /**
     * @param AclPermission $command
     * @return AclIdentity
     */
    private function getAclIdentity(AclPermission $command): AclIdentity
    {
        return new AclIdentity($command->getEntityClass(), $command->getEntityId(), $command->getEntityField());
    }

    /**
     * @param AclPermission $command
     * @return AclMask
     */
    private function getAclMask(AclPermission $command): AclMask
    {
        $aclMaskBuilder = new AclMaskBuilder();
        $aclMaskBuilder->add($command->getPermissions());
        return $aclMaskBuilder->get();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddAclPermission::class => [
            'method' => 'handleAddAclPermission',
            'bus' => 'command_bus'
        ];

        yield RemoveAclPermission::class => [
            'method' => 'handleRemoveAclPermission',
            'bus' => 'command_bus'
        ];
    }
}
