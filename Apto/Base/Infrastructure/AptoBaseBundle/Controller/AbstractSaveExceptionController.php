<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Exception;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractSaveExceptionController extends AbstractController
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var FileLocator
     */
    private $fileLocator;

    /**
     * @var HtmlErrorRenderer
     */
    private $htmlErrorRenderer;

    /**
     * @param KernelInterface $kernel
     * @param FileLocator $fileLocator
     * @param HtmlErrorRenderer $htmlErrorRenderer
     */
    public function __construct(KernelInterface $kernel, FileLocator $fileLocator, HtmlErrorRenderer $htmlErrorRenderer) {
        $this->kernel = $kernel;
        $this->fileLocator = $fileLocator;
        $this->htmlErrorRenderer = $htmlErrorRenderer;
    }

    /**
     * @param Exception $exception
     * @return string
     * @throws Exception
     */
    protected function saveException(Exception $exception): string
    {
        $html = $this->htmlErrorRenderer->render($exception);
        $exceptionUuid = Uuid::uuid4()->toString();
        file_put_contents($this->getLogPath() . $exceptionUuid . '.html', $html->getAsString());

        return $exceptionUuid;
    }

    /**
     * @return string
     */
    protected function getLogPath(): string
    {
        $logDir = $this->fileLocator->locate('@AptoBaseBundle/Logs/MessageBus/') . '/' . $this->kernel->getEnvironment() . '/';

        if (!file_exists($logDir)) {
            mkdir($logDir);
        }

        return $logDir;
    }

    /**
     * @param string $exceptionUuid
     * @return string
     */
    protected function getExceptionUrl(string $exceptionUuid): string
    {
        return $this->generateUrl('apto_base_infrastructure_aptobase_messagebus_messagebuslog', ['logId' => $exceptionUuid]);
    }
}