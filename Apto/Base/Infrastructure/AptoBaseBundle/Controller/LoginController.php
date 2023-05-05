<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Apto\Base\Infrastructure\AptoBaseBundle\Template\TemplateLoader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @var TemplateLoader
     */
    private TemplateLoader $templateLoader;

    /**
     * @var AuthenticationUtils
     */
    private AuthenticationUtils $authenticationUtils;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param TemplateLoader $templateLoader
     * @param AuthenticationUtils $authenticationUtils
     * @param Security $security
     */
    public function __construct(TemplateLoader $templateLoader, AuthenticationUtils $authenticationUtils, Security $security)
    {
        $this->templateLoader = $templateLoader;
        $this->authenticationUtils = $authenticationUtils;
        $this->security = $security;
    }

    /**
     * @Route("/backend/login", priority="10")
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request): Response
    {
        $templateLoaderData = $this->templateLoader->getData('backend');

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $csrfToken = $this->container->has('security.csrf.token_manager')
            ? $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        $currentUsername = $this->security->getUser() ? $this->security->getUser()->getUserIdentifier() : null;

        $templateVars = [
            'aptoEnvironment' => [
                'routes' => $templateLoaderData['routes']
            ],
            'templateLoaderData' => $templateLoaderData,
            'currentUsername' => $currentUsername,
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
            'perspectives' => $this->getParameter('perspectives'),
            'aptoApi' => $this->templateLoader->getApiData('backend')
        ];

        return $this->render('@AptoBase/apto/base/backend/pages/login/login.html.twig', $templateVars);
    }

    /**
     * @Route("/backend/logout", priority="10")
     * @param Request $request
     */
    public function logoutAction(Request $request)
    {

    }
}
