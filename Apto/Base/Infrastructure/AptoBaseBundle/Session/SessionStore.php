<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Session;

use Apto\Base\Application\Core\Service\SessionStoreInterface;
use Apto\Base\Infrastructure\AptoBaseBundle\Security\FrontendUser\FrontendUser;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class SessionStore implements SessionStoreInterface
{
    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    public function __construct(
        Security $security,
        SessionInterface $session
    ) {
        $this->security = $security;
        $this->session = $session;
    }

    /**
     * @return string|null
     */
    public function getFrontendUserId(): ?string
    {
        $user = $this->security->getUser();

        if ($user instanceof FrontendUser) {
            return $user->getId();
        }

        return null;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->session->getId();
    }

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $this->session->get($name, $default);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function set(string $name, $value)
    {
        return $this->session->set($name, $value);
    }
}