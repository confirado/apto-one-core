<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Apto\Base\Application\Backend\Commands\User\AcceptLicence;
use Apto\Base\Application\Backend\Query\UserLicence\UserLicenceFinder;
use Apto\Base\Domain\Backend\Model\User\UserName;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandBus;
use Apto\Base\Infrastructure\AptoBaseBundle\Template\TemplateLoader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class LicenceController extends AbstractController
{
    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var TemplateLoader
     */
    private TemplateLoader $templateLoader;

    /**
     * @var UserLicenceFinder
     */
    private UserLicenceFinder $userLicenceFinder;

    /**
     * @var CommandBus
     */
    private CommandBus $commandBus;

    /**
     * @param Security $security
     * @param TemplateLoader $templateLoader
     * @param UserLicenceFinder $userLicenceFinder
     * @param CommandBus $commandBus
     */
    public function __construct(Security $security, TemplateLoader $templateLoader, UserLicenceFinder $userLicenceFinder, CommandBus $commandBus)
    {
        $this->security = $security;
        $this->templateLoader = $templateLoader;
        $this->userLicenceFinder = $userLicenceFinder;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/backend/licence")
     *
     * @param Request $request
     * @return Response
     */
    public function licenceAction(Request $request): Response
    {
        $templateLoaderData = $this->templateLoader->getData('backend');

        $username = $this->security->getUser()->getUserIdentifier();

        $csrfToken = $this->container->has('security.csrf.token_manager')
            ? $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        // current licence
        $currentLicence = $this->userLicenceFinder->findCurrent();

        // @TODO shall we skip licence dialog if no database entry is found?
        if (!$currentLicence || $username === UserName::USERNAME_SUPERUSER) {
            return $this->redirectToRoute('apto_base_infrastructure_aptobase_backend_index');
        }

        $templateVars = [
            'aptoEnvironment' => [
                'routes' => $templateLoaderData['routes']
            ],
            'templateLoaderData' => $templateLoaderData,
            'currentUsername' => $username,
            'csrf_token' => $csrfToken,
            'licence' => $currentLicence,
            'perspectives' => $this->getParameter('perspectives'),
            'aptoApi' => $this->templateLoader->getApiData('backend')
        ];

        return $this->render('@AptoBase/apto/base/backend/pages/licence/licence.html.twig', $templateVars);
    }

    /**
     * @Route("/backend/licence/accept")
     *
     * @param Request $request
     * @return Response
     */
    public function licenceAcceptAction(Request $request): Response
    {
        $command = new AcceptLicence(
            $this->container->get('security.token_storage')->getToken()->getUsername(),
            $request->get('licence_id', null)
        );

        $this->commandBus->handle($command);

        return $this->redirectToRoute('apto_base_infrastructure_aptobase_backend_index');
    }
}
