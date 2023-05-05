<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse\ErrorMessageResponse;
use Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse\MessageResponse;
use Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse\SuccessMessageResponse;
use Exception;
use Throwable;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Application\Backend\Commands\MediaFile\RemoveMediaFileByName;
use Apto\Base\Application\Backend\Commands\MediaFile\RemoveMediaFileDirectory;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandBus;

class MediaController extends AbstractSaveExceptionController
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param KernelInterface $kernel
     * @param FileLocator $fileLocator
     * @param HtmlErrorRenderer $htmlErrorRenderer
     * @param CommandBus $commandBus
     * @param SerializerInterface $serializer
     */
    public function __construct(
        KernelInterface $kernel,
        FileLocator $fileLocator,
        HtmlErrorRenderer $htmlErrorRenderer,
        CommandBus $commandBus,
        SerializerInterface $serializer
    ) {
        parent::__construct($kernel, $fileLocator, $htmlErrorRenderer);
        $this->commandBus = $commandBus;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/backend/media/remove-directory", methods={"POST"})
     *
     * @param Request $request
     * @param MediaFileSystemConnector $mediaFileSystemConnector
     * @return Response
     * @throws Exception|Throwable
     */
    public function removeDirectoryAction(Request $request, MediaFileSystemConnector $mediaFileSystemConnector): Response
    {
        $content = json_decode($request->getContent(), true);
        $response = $this->removeDirectory(
            $content['directory'] ?? null,
            $mediaFileSystemConnector
        );

        return $response->getJsonResponse($this->serializer);
    }

    /**
     * @param null|string $path
     * @param MediaFileSystemConnector $mediaFileSystemConnector
     * @return MessageResponse
     * @throws Throwable
     */
    private function removeDirectory(?string $path, MediaFileSystemConnector $mediaFileSystemConnector): MessageResponse
    {
        if (null === $path) {
            $response = new ErrorMessageResponse(
                'RemoveDirectory',
                'No directory given.',
                0
            );
        } else {
            $start = microtime(true);
            $directory = Directory::createFromPath($path);

            try {
                $this->removeDirectoryFilesRecursive($directory, $mediaFileSystemConnector);
                $this->removeEmptyDirectoryRecursive($directory, $mediaFileSystemConnector);
                $duration = microtime(true) - $start;
                $response = new SuccessMessageResponse(
                    'RemoveDirectory',
                    'Directory "' . $path . '" was deleted successfully.',
                    $duration
                );
            } catch (Exception $e) {
                $duration = microtime(true) - $start;
                $exceptionUuid = $this->saveException($e);
                $response = ErrorMessageResponse::fromException(
                    'RemoveDirectory',
                    'Directory "' . $path . '" was not deleted successfully.',
                    $duration,
                    $e,
                    $exceptionUuid,
                    $this->getExceptionUrl($exceptionUuid)
                );
            }
        }

        return $response;
    }

    /**
     * @param Directory $directory
     * @param MediaFileSystemConnector $mediaFileSystemConnector
     * @return void
     * @throws Throwable
     */
    private function removeDirectoryFilesRecursive(Directory $directory, MediaFileSystemConnector $mediaFileSystemConnector)
    {
        $objects = $mediaFileSystemConnector->getDirectoryContent($directory);

        foreach ($objects as $object) {
            if ($object['isDir']) {
                $this->removeDirectoryFilesRecursive(Directory::createFromPath($object['path']), $mediaFileSystemConnector);
            } else {
                $this->commandBus->handle(
                    new RemoveMediaFileByName($object['path'])
                );
            }
        }
    }

    /**
     * @param Directory $directory
     * @param MediaFileSystemConnector $mediaFileSystemConnector
     * @return void
     * @throws Throwable
     */
    private function removeEmptyDirectoryRecursive(Directory $directory, MediaFileSystemConnector $mediaFileSystemConnector)
    {
        $objects = $mediaFileSystemConnector->getDirectoryContent($directory);

        foreach ($objects as $object) {
            if ($object['isDir']) {
                $this->removeEmptyDirectoryRecursive(Directory::createFromPath($object['path']), $mediaFileSystemConnector);
            }
        }

        $this->commandBus->handle(
            new RemoveMediaFileDirectory($directory->getPath())
        );
    }
}
