<?php

namespace Apto\Base\Domain\Frontend\Commands;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Application\Core\EventBusInterface;
use Apto\Base\Domain\Core\Model\Auth\PasswordReset;
use Apto\Base\Domain\Core\Model\Auth\PasswordResetRepository;
use Apto\Base\Domain\Core\Model\FrontendUser\FrontendUserRepository;
use Apto\Base\Domain\Core\Service\Exception\InvalidTokenException;
use Apto\Base\Domain\Core\Service\PasswordEncoder;
use Apto\Base\Domain\Frontend\Events\ResetPasswordTokenCreated;

class ResetPasswordHandler implements CommandHandlerInterface
{
    private FrontendUserRepository $frontendUserRepository;
    private PasswordResetRepository $passwordResetRepository;
    private PasswordEncoder $passwordEncoder;
    private EventBusInterface $eventBus;

    public function __construct(
        FrontendUserRepository $frontendUserRepository,
        PasswordResetRepository $passwordResetRepository,
        PasswordEncoder $passwordEncoder,
        EventBusInterface $eventBus,
    )
    {
        $this->frontendUserRepository = $frontendUserRepository;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->eventBus = $eventBus;
    }

    public function handleResetPassword(ResetPassword $resetPasswordCommand): void
    {
        $user = $this->frontendUserRepository->findOneByEmail($resetPasswordCommand->getEmail());

        if (!$user) {
            return;
        }
        $passwordReset = new PasswordReset($user->getEmail()->getEmail(), rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));

        $passwordReset = $this->passwordResetRepository->add($passwordReset);
        $this->eventBus->handle(new ResetPasswordTokenCreated($passwordReset->getEmail(), $passwordReset->getToken()));
    }

    public function handleChangePasswordWithToken(ChangePasswordWithToken $changePasswordCommand): void
    {
        $passwordReset = $this->passwordResetRepository->findOneByToken($changePasswordCommand->getToken());

        if (!$passwordReset) {
            throw new InvalidTokenException('Invalid update password token');
        }

        $user = $this->frontendUserRepository->findOneByEmail($passwordReset->getEmail());

        if (!$user) {
            throw new InvalidTokenException('Invalid update password token');
        }

        $user = $user->setPassword($this->passwordEncoder->encodePassword($changePasswordCommand->getPassword()));
        $this->frontendUserRepository->update($user);
        $user->publishEvents();
        $this->passwordResetRepository->remove($passwordReset);
    }

    public static function getHandledMessages(): iterable
    {
        yield ResetPassword::class => [
            'method' => 'handleResetPassword',
            'bus' => 'command_bus'
        ];

        yield ChangePasswordWithToken::class => [
            'method' => 'handleChangePasswordWithToken',
            'bus' => 'command_bus'
        ];
    }
}
