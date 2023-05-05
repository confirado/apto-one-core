<?php

namespace Apto\Base\Application\Backend\Commands\User;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Backend\Model\User\UserName;
use Apto\Base\Domain\Backend\Model\User\UserRepository;
use Apto\Base\Domain\Backend\Model\UserLicence\UserLicenceDocument;
use Apto\Base\Domain\Backend\Model\UserLicence\UserLicenceRepository;

class AcceptLicenceHandler implements CommandHandlerInterface
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserLicenceRepository
     */
    protected $userLicenceRepository;

    /**
     * AcceptLicenceHandler constructor.
     * @param UserRepository $userRepository
     * @param UserLicenceRepository $userLicenceRepository
     */
    public function __construct(
        UserRepository $userRepository,
        UserLicenceRepository $userLicenceRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userLicenceRepository = $userLicenceRepository;
    }

    /**
     * @param AcceptLicence $command
     * @throws \Exception
     */
    public function handle(AcceptLicence $command)
    {
        // superadmins do not need licences :-P
        if ($command->getUsername() === UserName::USERNAME_SUPERUSER) {
            return;
        }

        $user = $this->userRepository->findOneByUsername($command->getUsername());
        $licence = $this->userLicenceRepository->findById($command->getUserLicenceId());

        if (!$user || !$licence) {
            return;
        }

        // create document and signature
        $userLicenceDocument = new UserLicenceDocument(
            $licence->getTitle(),
            $licence->getText(),
            $user->getUsername()
        );

        $user->setUserLicenceSignature($userLicenceDocument->sign());
        $user->publishEvents();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AcceptLicence::class => [
            'method' => 'handle',
            'bus' => 'command_bus'
        ];
    }
}