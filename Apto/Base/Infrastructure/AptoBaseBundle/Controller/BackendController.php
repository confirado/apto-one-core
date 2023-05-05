<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Apto\Base\Application\Backend\Query\UserLicence\UserLicenceFinder;
use Apto\Base\Domain\Backend\Model\User\UserName;
use Apto\Base\Domain\Backend\Model\UserLicence\UserLicenceDocument;
use Apto\Base\Domain\Backend\Model\UserLicence\UserLicenceSignature;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Infrastructure\AptoBaseBundle\Template\TemplateLoader;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;

class BackendController extends AbstractController
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
     * @param Security $security
     * @param TemplateLoader $templateLoader
     * @param UserLicenceFinder $userLicenceFinder
     */
    public function __construct(Security $security, TemplateLoader $templateLoader, UserLicenceFinder $userLicenceFinder)
    {
        $this->security = $security;
        $this->templateLoader = $templateLoader;
        $this->userLicenceFinder = $userLicenceFinder;
    }

    /**
     * @Route("/backend", priority=10)
     * @param Request $request
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function indexAction(Request $request): Response
    {
        // redirect for invalid licences
        if (!$this->hasValidUserLicence()) {
            return $this->redirectToRoute('apto_base_infrastructure_aptobase_licence_licence');
        }

        $templateLoaderData = $this->templateLoader->getData('backend');
        $templateVars = [
            'templateLoaderData' => $templateLoaderData,
            'currentUsername' => $this->container->get('security.token_storage')->getToken()->getUserIdentifier(),
            'aptoEnvironment' => [
                'routes' => $templateLoaderData['routes'],
                'upload' => [
                    'maxFiles' => ini_get('max_file_uploads'),
                    'maxFileSize' => min(
                        $this->convertPHPSizeToBytes(ini_get('post_max_size')),
                        $this->convertPHPSizeToBytes(ini_get('upload_max_filesize')),
                        $this->convertPHPSizeToBytes(ini_get('memory_limit'))
                    ),
                    'maxTotalSize' => min(
                        $this->convertPHPSizeToBytes(ini_get('post_max_size')),
                        $this->convertPHPSizeToBytes(ini_get('memory_limit'))
                    )
                ]
            ],
            'perspectives' => $this->getParameter('perspectives'),
            'aptoApi' => $this->templateLoader->getApiData('backend')
        ];

        return $this->render('@AptoBase/apto/base/backend/index.html.twig', $templateVars);
    }

    /**
     * @Route("/backend/uuid")
     * @Route("/backend/uuid/{number}")
     * @param Request $request
     *
     * @return Response
     */
    public function createUuidsAction(Request $request): Response
    {
        $number = $request->get('number', 1);
        echo '<br />';
        for($i = 0; $i < $number; $i++) {
            $uuid = new AptoUuid();
            echo $uuid->getId();
            echo '<br />';
        }
        exit;
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function hasValidUserLicence(): bool
    {
        $user = $this->security->getUser();

        // super users do not need any licence
        if ($user->getUserIdentifier() === UserName::USERNAME_SUPERUSER) {
            return true;
        }

        // get current valid licence
        $currentLicence = $this->userLicenceFinder->findCurrent();

        // @TODO shall we default to valid if no licence has been found in database?
        // no licence defaults to valid licence
        if (!$currentLicence) {
            return true;
        }

        // user has never accepted any licence before
        if (!$user->getUserLicenceHash() || !$user->getUserLicenceSignatureTimestamp()) {
            return false;
        }

        // create document and signature
        $userLicenceDocument = new UserLicenceDocument(
            $currentLicence['title'],
            $currentLicence['text'],
            $user->getUserIdentifier()
        );
        $signature = new UserLicenceSignature(
            $user->getUserLicenceHash(),
            $user->getUserLicenceSignatureTimestamp()
        );

        // is licence valid?
        return $userLicenceDocument->validateSignature($signature);
    }

    /**
     * @param $size
     * @return int|string
     */
    protected function convertPHPSizeToBytes($size)
    {
        if (is_numeric($size)) {
           return $size;
        }
        $suffix = substr($size, -1);
        $value = substr($size, 0, -1);
        switch(strtoupper($suffix)){
            case 'P':
                $value *= 1024;
            case 'T':
                $value *= 1024;
            case 'G':
                $value *= 1024;
            case 'M':
                $value *= 1024;
            case 'K':
                $value *= 1024;
                break;
        }
        return $value;
    }
}
