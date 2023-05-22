<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Command;

use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandBus;
use Apto\Base\Infrastructure\AptoBaseBundle\Security\User\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class AptoContainerAwareCommand extends Command
{
    /**
     * @var User
     */
    protected User $user;

    /**
     * @var CommandBus
     */
    protected CommandBus $commandBus;

    /**
     * @var TokenStorageInterface
     */
    protected TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage, CommandBus $commandBus, string $name = null)
    {
        parent::__construct($name);
        $this->tokenStorage = $tokenStorage;
        $this->commandBus = $commandBus;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->user = new User($this->getName(), $this->getName(), $this->getName(), '', true, [['identifier' => 'ROLE_APTO_CLI_COMMAND']], null, null, null);

        $token = new UsernamePasswordToken($this->user, null, ['ROLE_APTO_CLI_COMMAND']);
        $this->tokenStorage->setToken($token);
    }
}
