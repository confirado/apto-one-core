<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Apto\Base\Infrastructure\AptoBaseBundle\Security\FrontendUser\FrontendUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FrontendLoginController extends AbstractController
{
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
