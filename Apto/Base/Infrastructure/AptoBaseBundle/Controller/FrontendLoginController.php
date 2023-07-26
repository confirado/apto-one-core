<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Apto\Base\Application\Backend\Query\FrontendUser\FrontendUserFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Security\FrontendUser\FrontendUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FrontendLoginController extends AbstractController
{
    /**
     * @var FrontendUserFinder
     */
    private FrontendUserFinder $frontendUserFinder;

    /**
     * @param FrontendUserFinder $frontendUserFinder
     */
    public function __construct(FrontendUserFinder $frontendUserFinder)
    {
        $this->frontendUserFinder = $frontendUserFinder;
    }

    /**
     * @Route("/login", name="frontend_login")
     * @Route("/current-user", name="current-user")
     */
    public function loginAction(Request $request)
    {
        $user = $this->getUser();

        if($user instanceof FrontendUser) {
            return $this->json([
                'username' => $user->getUserIdentifier(),
                'user' => $this->frontendUserFinder->findByUsername($user->getUserIdentifier()),
                'roles' => $user->getRoles(),
                'isLoggedIn' => true
            ]);
        }

        return $this->json([
            'isLoggedIn' => false
        ]);
    }

    /**
     * @Route("/logout")
     * @param Request $request
     */
    public function logoutAction(Request $request)
    {
    }
}
