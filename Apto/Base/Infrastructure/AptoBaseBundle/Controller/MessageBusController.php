<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse\DeniedMessageResponse;
use Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse\ErrorMessageResponse;
use Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse\MessageResponse;
use Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse\SuccessMessageResponse;
use Exception;
use Throwable;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use Apto\Base\Application\Backend\Query\User\FindUserByApiOrigin;
use Apto\Base\Application\Core\Query\MessageBusMessage\FindMessageBusMessages;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\MessageBusFirewall;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\MessageBusManager;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryBus;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandBus;

class MessageBusController extends AbstractSaveExceptionController
{
    /**
     * @var MessageBusFirewall
     */
    private $messageBusFirewall;

    /**
     * @var MessageBusManager
     */
    private $messageBusManager;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var QueryBus
     */
    private $queryBus;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param KernelInterface $kernel
     * @param FileLocator $fileLocator
     * @param HtmlErrorRenderer $htmlErrorRenderer
     * @param MessageBusFirewall $messageBusFirewall
     * @param MessageBusManager $messageBusManager
     * @param CommandBus $commandBus
     * @param QueryBus $queryBus
     * @param SerializerInterface $serializer
     */
    public function __construct(
        KernelInterface $kernel,
        FileLocator $fileLocator,
        HtmlErrorRenderer $htmlErrorRenderer,
        MessageBusFirewall $messageBusFirewall,
        MessageBusManager $messageBusManager,
        CommandBus $commandBus,
        QueryBus $queryBus,
        SerializerInterface $serializer
    ) {
        parent::__construct($kernel, $fileLocator, $htmlErrorRenderer);
        $this->messageBusFirewall = $messageBusFirewall;
        $this->messageBusManager = $messageBusManager;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api", methods={"OPTIONS"})
     *
     * @param Request $request
     * @return Response
     * @throws Exception|Throwable
     */
    public function apiOptionsAction(Request $request): Response
    {
        $user = $this->getUserByOriginFromRequest($request);

        if (null === $user) {
            return new Response('', 403);
        }

        $headers = [
            'Content-Type' => 'application/json',
            'P3P' => 'CP="This is not a privacy policy!"',
            'Access-Control-Allow-Origin' => $user['apiOrigin'],
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'POST',
            'Access-Control-Allow-Headers' => 'Content-Type, X-AUTH-TOKEN'
        ];

        return new Response('', 204, $headers);
    }

    /**
     * @Route("/api", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     * @throws Exception|Throwable
     */
    public function apiAction(Request $request): Response
    {
        // @todo need some research how we can detect a curl request here, because if its a curl request no origin is send
        $user = $this->getUserByOriginFromRequest($request);
        $data = $this->getData($request);

        // set headers
        $headers = [
            'Content-Type' => 'application/json',
            'P3P' => 'CP="This is not a privacy policy!"',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'POST',
            'Access-Control-Allow-Headers' => 'Content-Type, X-AUTH-TOKEN'
        ];

        if (null !== $user) {
            $headers['Access-Control-Allow-Origin'] = $user['apiOrigin'];
        }

        $response = new Response('', 400, $headers);

        if (array_key_exists('command', $data)) {
            $response = $this->commandAction($request);
        }

        if (array_key_exists('query', $data)) {
            $response = $this->queryAction($request);
        }

        return new Response($response->getContent(), $response->getStatusCode(), $headers);
    }

    /**
     * @Route("/message-bus/command", methods={"POST"})
     * @Route("/backend/message-bus/command", methods={"POST"}, name="apto_base_infrastructure_aptobase_messagebus_command_backend")
     *
     * @param Request $request
     * @return Response
     * @throws Exception|Throwable
     */
    public function commandAction(Request $request): Response
    {
        $data = $this->getData($request);
        $files = $this->getFiles($request);
        $response = $this->executeCommand(
            $data['command'],
            $data['arguments'],
            $files
        );

        return $response->getJsonResponse($this->serializer);
    }

    /**
     * @Route("/message-bus/query", methods={"POST"})
     * @Route("/backend/message-bus/query", methods={"POST"}, name="apto_base_infrastructure_aptobase_messagebus_query_backend")
     *
     * @param Request $request
     * @return Response
     * @throws Exception|Throwable
     */
    public function queryAction(Request $request): Response
    {
        $data = $this->getData($request);
        $response = $this->executeQuery(
            $data['query'],
            $data['arguments']
        );

        return $response->getJsonResponse($this->serializer);
    }

    /**
     * @Route("/message-bus/batch-execute", methods={"POST"})
     * @Route("/backend/message-bus/batch-execute", methods={"POST"}, name="apto_base_infrastructure_aptobase_messagebus_batchexecute_backend")
     *
     * @param Request $request
     * @return Response
     * @throws Exception|Throwable
     */
    public function batchExecuteAction(Request $request): Response
    {
        $data = $this->getData($request);
        $response = $this->executeBatch(
            $data['messages']
        );

        return $response->getJsonResponse($this->serializer);
    }

    /**
     * @Route("/message-bus/messages-is-granted", methods={"POST"})
     * @Route("/backend/message-bus/messages-is-granted", methods={"POST"}, name="apto_base_infrastructure_aptobase_messagebus_messagesisgranted_backend")
     *
     * @param Request $request
     * @return Response
     * @throws Exception|Throwable
     */
    public function messagesIsGrantedAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            $data = $this->getAllCommandsAndQueries();
        }
        $response = $this->executeMessagesIsGranted(
            $data['commands'],
            $data['queries']
        );

        return $response->getJsonResponse($this->serializer);

    }

    /**
     * @Route("/message-bus/log/{logId}", methods={"GET"})
     * @Route("/backend/message-bus/log/{logId}", methods={"GET"})
     *
     * @param string $logId
     * @return Response
     */
    public function messageBusLogAction(string $logId): Response
    {
        $exceptionLogFile = $this->getLogPath() . $logId . '.html';
        if (file_exists($exceptionLogFile)) {
            $exceptionHtml = file_get_contents($exceptionLogFile);
            return new Response($exceptionHtml);
        }
        return new Response('Log not found!');
    }

    /**
     * @Route("/message-bus/setLocale", methods={"POST"})
     * @Route("/backend/message-bus/setLocale", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function setLocaleAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $response = new SuccessMessageResponse(
            'SetLocale',
            'SetLocale wurde erfolgreich ausgeführt.',
            0,
            [
                'currentLocale' => $request->getLocale(),
                'setLocale' => $data['_locale']
            ]
        );

        return $response->getJsonResponse($this->serializer);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getData(Request $request): array
    {
        if ($this->isUploadRequest($request)) {
            $type = $request->get('type');
            $message = $request->get('message');
            $command = $request->get('command');
            $arguments = $request->get('arguments', []);

            // is api call
            if (null !== $type && null !== $message) {
                return [
                    $type => $message,
                    'arguments' => $arguments
                ];
            }

            // is command call
            if (null !== $command) {
                {
                    return [
                        'command' => $command,
                        'arguments' => $arguments
                    ];
                }
            }
        } else {
            $data = json_decode($request->getContent(), true);

            // is batch execute call
            if (array_key_exists('messages', $data)) {
                return $data;
            }

            // ensure existing arguments, populate with empty array if not defined
            if (!array_key_exists('arguments', $data)) {
                $data['arguments'] = [];
            }

            // is api call
            if (array_key_exists('type', $data) && array_key_exists('message', $data)) {
                $data[$data['type']] = $data['message'];
                unset($data['type']);
                unset($data['message']);
                return $data;
            }

            // is command or query call
            if (array_key_exists('command', $data) || array_key_exists('query', $data)) {
                return $data;
            }
        }

        return [];
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isUploadRequest(Request $request): bool
    {
        $hasTypeAndMessage = false;
        $hasCommand = false;

        if (null !== $request->get('type') && null !== $request->get('message')) {
            $hasTypeAndMessage = true;
        }

        if (null !== $request->get('command')) {
            $hasCommand = true;
        }

        if ($hasTypeAndMessage || $hasCommand){
            return true;
        }
        return false;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getFiles(Request $request): array
    {
        $files = [];

        if ($this->isUploadRequest($request)) {
            /** @var UploadedFile $file */
            foreach ($request->files as $file) {
                if (is_array($file)) {
                    /** @var UploadedFile $subFile */
                    foreach ($file as $subFile) {
                        $files[$subFile->getPathname()] = $subFile->getClientOriginalName();
                    }
                } else {
                    $files[$file->getPathname()] = $file->getClientOriginalName();
                }
            }
        }

        return $files;
    }

    /**
     * @param Request $request
     * @return array|null
     * @throws Throwable
     */
    private function getUserByOriginFromRequest(Request $request): ?array
    {
        $user = null;
        $origin = $request->headers->get('origin');

        if (null === $origin) {
            return null;
        }

        $query = new FindUserByApiOrigin($origin);

        $this->queryBus->handle($query, $user);

        return $user;
    }


    /**
     * Execute a command with given arguments
     * @param string $commandName
     * @param array $arguments
     * @param array $files
     * @return MessageResponse
     * @throws Throwable
     */
    private function executeCommand(string $commandName, array $arguments, array $files = []): MessageResponse
    {
        $start = 0;

        try {
            $command = $this->messageBusManager->getCommand(
                $commandName,
                $arguments,
                $files
            );

            if ($this->messageBusFirewall->commandExecutionAllowed($command)) {
                $start = microtime(true);
                $this->commandBus->handle($command);
                $duration = microtime(true) - $start;

                $response = new SuccessMessageResponse(
                    $commandName,
                    'Command {' . $commandName . '} wurde erfolgreich ausgeführt.',
                    $duration
                );
            } else {
                $response = new DeniedMessageResponse(
                    $commandName,
                    'Command {' . $commandName . '} wurde abgelehnt.'
                );
            }
        } catch (Exception $e) {
            $duration = $start > 0 ? (microtime(true) - $start) : 0;
            $exceptionUuid = $this->saveException($e);
            $response = ErrorMessageResponse::fromException(
                $commandName,
                'Command {' . $commandName . '} schlug fehl.',
                $duration,
                $e,
                $exceptionUuid,
                $this->getExceptionUrl($exceptionUuid)
            );
        }

        return $response;
    }

    /**
     * Execute a query with given arguments
     * @param string $queryName
     * @param array $arguments
     * @return MessageResponse
     * @throws Throwable
     */
    private function executeQuery(string $queryName, array $arguments): MessageResponse
    {
        $start = 0;

        $queryResult = [];
        try {
            $query = $this->messageBusManager->getQuery(
                $queryName,
                $arguments
            );

            if ($this->messageBusFirewall->queryExecutionAllowed($query)) {
                $start = microtime(true);
                $this->queryBus->handle($query, $queryResult);
                $duration = microtime(true) - $start;

                $response = new SuccessMessageResponse(
                    $queryName,
                    'Query {' . $queryName . '} wurde erfolgreich ausgeführt.',
                    $duration,
                    ['result' => $queryResult]
                );
            } else {
                $response = new DeniedMessageResponse(
                    $queryName,
                    'Query {' . $queryName . '} wurde abgelehnt.'
                );
            }
        } catch (Exception $e) {
            $duration = $start > 0 ? (microtime(true) - $start) : 0;
            $exceptionUuid = $this->saveException($e);
            $response = ErrorMessageResponse::fromException(
                $queryName,
                'Query {' . $queryName . '} schlug fehl.',
                $duration,
                $e,
                $exceptionUuid,
                $this->getExceptionUrl($exceptionUuid)
            );
        }

        return $response;
    }

    /**
     * @param array $messages
     * @return MessageResponse
     * @throws Throwable
     */
    private function executeBatch(array $messages): MessageResponse
    {
        $start = microtime(true);

        try {
            $results = [];
            foreach ($messages as $message) {

                $arguments = $message['arguments'] ?? [];
                $result = null;

                // execute command
                if (array_key_exists('command', $message)) {
                    $result = $this->executeCommand($message['command'], $arguments);
                }

                // execute query
                if (array_key_exists('query', $message)) {
                    $result = $this->executeQuery($message['query'], $arguments);
                }

                $results[] = $result;

                if ($result instanceof ErrorMessageResponse) {
                    throw new Exception(sprintf(
                        'BatchExecute durch Ausführungsfehler in %s. Message gestoppt.',
                        count($results)
                    ));
                }
            }

            $duration = microtime(true) - $start;
            $response = new SuccessMessageResponse(
                'BatchExecute',
                'BatchExecute erfolgreich ausgeführt.',
                $duration,
                ['result' => $results]
            );

        } catch (Exception $e) {
            $duration = microtime(true) - $start;
            $exceptionUuid = $this->saveException($e);
            $response = ErrorMessageResponse::fromException(
                'BatchExecute',
                'BatchExecute schlug fehl.',
                $duration,
                $e,
                $exceptionUuid,
                $this->getExceptionUrl($exceptionUuid)
            );
        }

        return $response;
    }

    /**
     * @param array $commands
     * @param array $queries
     * @return MessageResponse
     * @throws Exception
     */
    private function executeMessagesIsGranted(array $commands, array $queries): MessageResponse
    {
        $start = microtime(true);

        try {
            $allMessagesGranted = true;
            $anyMessageGranted = false;
            $messagesGranted = [];

            // get commands
            foreach ($commands as $command) {
                $commandClass = $this->messageBusManager->getCommandClass($command);
                $granted = $this->messageBusFirewall->commandGranted($commandClass);
                $messagesGranted['commands'][$command] = $granted;
                if ($granted) {
                    $anyMessageGranted = true;
                } else {
                    $allMessagesGranted = false;
                }
            }

            // get queries
            foreach ($queries as $query) {
                $queryClass = $this->messageBusManager->getQueryClass($query);
                $granted = $this->messageBusFirewall->queryGranted($queryClass);
                $messagesGranted['queries'][$query] = $granted;
                if ($granted) {
                    $anyMessageGranted = true;
                } else {
                    $allMessagesGranted = false;
                }
            }

            $duration = microtime(true) - $start;
            $response = new SuccessMessageResponse(
                'MessagesIsGranted',
                'MessagesIsGranted erfolgreich ausgeführt.',
                $duration,
                [
                    'result' => [
                        'messagesAllGranted' => $allMessagesGranted,
                        'messagesOneGranted' => $anyMessageGranted,
                        'messagesGranted' => $messagesGranted
                    ]
                ]
            );
        } catch (Exception $e) {
            $exceptionUuid = $this->saveException($e);
            $response = ErrorMessageResponse::fromException(
                'MessagesIsGranted',
                'MessagesIsGranted schlug fehl.',
                $duration,
                $e,
                $exceptionUuid,
                $this->getExceptionUrl($exceptionUuid)
            );
        }

        return $response;
    }

    /**
     * Get all commands and queries
     * @return array[]
     * @throws Throwable
     */
    private function getAllCommandsAndQueries(): array
    {
        $findMessageBusMessages = new FindMessageBusMessages();
        $messageBusMessages = null;
        $this->queryBus->handle($findMessageBusMessages, $messageBusMessages);

        $commands = [];
        foreach ($messageBusMessages['commands'] as $command => $commandClass) {
            $commands[] = $command;
        }

        $queries = [];
        foreach ($messageBusMessages['queries'] as $query => $queryClass) {
            $queries[] = $query;
        }

        return [
            'commands' => $commands,
            'queries' => $queries
        ];
    }
}
