<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Service\PasswordEncoder;
use Throwable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Requirements\ProjectRequirements;
use Symfony\Requirements\Requirement;
use Symfony\Requirements\SymfonyRequirements;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandBus;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Apto\Base\Infrastructure\AptoBaseBundle\Template\InstallTemplateLoader;
use Apto\Catalog\Application\Backend\Commands\Shop\UpdateShopDomain;
use Apto\Catalog\Application\Backend\Commands\Shop\UpdateShopName;
use Apto\Catalog\Application\Backend\Commands\Shop\UpdateShopOperator;
use Apto\Base\Application\Backend\Commands\User\AddUser;

class InstallController extends AbstractController
{
    private const SHOP_ID = '133c31ee-7fd3-4032-a27d-a6959dd31aac';
    private const ADMIN_GROUP_ID = '80368e25-ab66-4833-aa4c-b1496e3aa1b4';

    /**
     * @var CommandBus
     */
    private CommandBus $commandBus;

    /**
     * @var InstallTemplateLoader
     */
    private InstallTemplateLoader $templateLoader;

    /**
     * @var PasswordEncoder
     */
    protected PasswordEncoder $passwordEncoder;

    /**
     * @var string
     */
    private string $projectDir;

    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var bool
     */
    private bool $installer;

    /**
     * @param CommandBus $commandBus
     * @param InstallTemplateLoader $templateLoader
     * @param PasswordEncoder $passwordEncoder
     * @param AptoParameterInterface $aptoParameter
     * @param Connection $connection
     */
    public function __construct(
        CommandBus $commandBus,
        InstallTemplateLoader $templateLoader,
        PasswordEncoder $passwordEncoder,
        AptoParameterInterface $aptoParameter,
        Connection $connection
    ) {
        $this->commandBus = $commandBus;
        $this->templateLoader = $templateLoader;
        $this->passwordEncoder = $passwordEncoder;
        $this->projectDir = $aptoParameter->get('kernel.project_dir');
        $this->connection = $connection;
        $this->installer = $aptoParameter->get('apto_installer') === 'enabled';
    }

    /**
     * @Route("/apto/install/")
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function aptoInstallAction(Request $request): Response
    {
        // assert installer is enabled
        if ($this->installer === false) {
            return $this->redirectToRoute('apto_base_infrastructure_aptobase_frontend_index');
        }

        // get locale
        $locale = $request->getLocale();

        // get template data
        $templateLoaderData = $this->templateLoader->getData('install');

        // assign template vars
        $templateVars = [
            'locale' => $locale,
            'templateLoaderData' => $templateLoaderData,
            'aptoApi' => $this->templateLoader->getApiData('install')
        ];

        // render template
        return $this->render('@AptoBase/apto/base/install/index.html.twig', $templateVars);
    }

    /**
     * @Route("/apto/install/check-requirements")
     * @param Request $request
     * @return Response
     */
    public function checkRequirementsAction(Request $request): Response
    {
        // assert installer is enabled
        if ($this->installer === false) {
            return $this->redirectToRoute('apto_base_infrastructure_aptobase_frontend_index');
        }

        $symfonyRequirements = new SymfonyRequirements();
        $projectRequirements = new ProjectRequirements($this->projectDir);

        // get requirements
        $requirements = [];
        /** @var Requirement $requirement */
        foreach (array_merge($symfonyRequirements->getRequirements(), $projectRequirements->getRequirements()) as $requirement) {
            if (!$requirement->isFulfilled()) {
                $requirements[] = $this->requirementToArray($requirement);
            }
        }

        // get recommendations
        $recommendations = [];
        /** @var Requirement $recommendation */
        foreach (array_merge($symfonyRequirements->getRecommendations(), $projectRequirements->getRecommendations()) as $recommendation) {
            if (!$recommendation->isFulfilled()) {
                $recommendations[] = $this->requirementToArray($recommendation);
            }
        }

        // return json response
        return new JsonResponse([
            'requirements' => $requirements,
            'recommendations' => $recommendations
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/apto/install/check-db-connection", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function checkDbConnectionAction(Request $request): Response
    {
        // assert installer is enabled
        if ($this->installer === false) {
            return $this->redirectToRoute('apto_base_infrastructure_aptobase_frontend_index');
        }

        $result = ['success' => true, 'message' => 'Datenbankverbindung erfolgreich getestet.'];
        try {
            $data = json_decode($request->getContent(), true);
            $this->connection->getDriver()->connect($data);
        } catch (\Exception $exception) {
            $result = ['success' => false, 'message' => $exception->getMessage()];
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * @Route("/apto/install/check-mail-delivery", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function checkMailDeliveryAction(Request $request): Response
    {
        // assert installer is enabled
        if ($this->installer === false) {
            return $this->redirectToRoute('apto_base_infrastructure_aptobase_frontend_index');
        }

        $result = ['success' => true, 'message' => 'Mail erfolgreich gesendet.'];
        try {
            $data = json_decode($request->getContent(), true);
            $mailDsn = sprintf('smtp://%s:%s@%s:%s', $data['user'], urlencode($data['password']), $data['host'], $data['port']);
            $mailer = new Mailer(Transport::fromDsn($mailDsn));
            $email = new Email();
            $email
                ->from(
                    new Address(
                        $data['fromMail'],
                        $data['fromName']
                    )
                )
                ->to(
                    new Address(
                        $data['fromMail'],
                        $data['fromName']
                    )
                )
                ->subject(
                    'Apto.ONE Installation'
                )
                ->text(
                    'Ich bin eine Testmail aus dem Installationstool!'
                )
            ;
            $mailer->send($email);
        } catch (\Exception $exception) {
            $result = ['success' => false, 'message' => $exception->getMessage()];
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * @Route("/apto/install/update-config-file", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function updateConfigFileAction(Request $request): Response
    {
        // assert installer is enabled
        if ($this->installer === false) {
            return $this->redirectToRoute('apto_base_infrastructure_aptobase_frontend_index');
        }

        $result = ['success' => true, 'message' => 'Datenbank erfolgreich importiert.'];
        try {
            $data = json_decode($request->getContent(), true);

            // write config file
            $publicFolder = "APTO_PUBLIC_FOLDER='" . $this->templateLoader->getBasePath() . "'\n";
            $dataBaseUrl = "DATABASE_URL='" . $this->getDatabaseUrl($data['database']) . "'\n";
            $mailerDsn = '';
            if (array_key_exists('mail', $data)) {
                $mailerDsn = "MAILER_DSN='" . $this->getMailerDsn($data['mail']) . "'\n";
            }
            file_put_contents($this->projectDir . '/.env.local', $publicFolder . $dataBaseUrl .  $mailerDsn);

            // import db
            $data['database']['charset'] = 'utf8';
            $clearSql = file_get_contents($this->projectDir . '/_db_/apto-clear-tables.sql');
            $importSql = file_get_contents($this->projectDir . '/_db_/apto-example-data.sql');
            $db = $this->connection->getDriver()->connect($data['database']);
            $db->exec($clearSql);
            $db->exec($importSql);
        } catch (\Exception $exception) {
            $result = ['success' => false, 'message' => $exception->getMessage()];
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * @Route("/apto/install/finish-installation", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function finishInstallationAction(Request $request): Response
    {
        // assert installer is enabled
        if ($this->installer === false) {
            return $this->redirectToRoute('apto_base_infrastructure_aptobase_frontend_index');
        }

        $result = ['success' => true, 'message' => 'Installation erfolgreich abgeschlossen.'];
        try {
            $data = json_decode($request->getContent(), true);

            // update shop and add user
            $this->updateShop($data);
            $this->addUser($data);

            $result['superadmin'] = (new AptoUuid())->getId();
            // disable installer and set superadmin
            file_put_contents(
                $this->projectDir . '/.env.local',
                "APTO_INSTALLER=disabled\nSA_HASH='" . $this->passwordEncoder->encodePassword($result['superadmin']) . "'\n" . file_get_contents($this->projectDir . '/.env.local')
            );
        } catch (\Exception $exception) {
            $result = ['success' => false, 'message' => $exception->getMessage()];
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * @param array $data
     * @return void
     * @throws Throwable
     */
    private function updateShop(array $data)
    {
        $updateShopName = new UpdateShopName(self::SHOP_ID, $data['name']);
        $updateShopDomain = new UpdateShopDomain(self::SHOP_ID, $data['domain']);
        $updateShopOperator = new UpdateShopOperator(self::SHOP_ID, $data['operatorMail'], $data['operatorName']);

        $this->commandBus->handle($updateShopName);
        $this->commandBus->handle($updateShopDomain);
        $this->commandBus->handle($updateShopOperator);
    }

    /**
     * @param array $data
     * @return void
     * @throws Throwable
     */
    private function addUser(array $data)
    {
        $email = '42@example.com';
        if (isset($data['operatorMail'])) {
            $email = $data['operatorMail'];
        }

        $command = new AddUser(
            true,
            $data['user'],
            $data['password'],
            $email,
            [['id' => self::ADMIN_GROUP_ID]],
            'trumbowyg'
        );

        $this->commandBus->handle($command);
    }

    /**
     * @param Requirement $requirement
     * @return array
     */
    private function requirementToArray(Requirement $requirement): array
    {
        return [
            'fulfilled' => $requirement->isFulfilled(),
            'testMessage' => $requirement->getTestMessage(),
            'helpHtml' => $requirement->getHelpHtml(),
            'helpText' => $requirement->getHelpText(),
            'optional' => $requirement->isOptional()
        ];
    }

    /**
     * @param array $data
     * @return string
     */
    private function getDatabaseUrl(array $data): string
    {
        return sprintf('mysql://%s:%s@%s:%s/%s', $data['user'], urlencode($data['password']), $data['host'], $data['port'], $data['dbname']);
    }

    /**
     * @param array $data
     * @return string
     */
    private function getMailerDsn(array $data): string
    {
        return sprintf('smtp://%s:%s@%s:%s', $data['user'], urlencode($data['password']), $data['host'], $data['port']);
    }
}
