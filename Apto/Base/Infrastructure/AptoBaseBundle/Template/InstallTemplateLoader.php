<?php
namespace Apto\Base\Infrastructure\AptoBaseBundle\Template;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;

class InstallTemplateLoader extends TemplateLoader
{
    /**
     * @var string
     */
    private string $basePath;

    /**
     * @param RouterInterface $router
     * @param KernelInterface $kernel
     * @param RequestStore $requestStore
     * @param AptoParameterInterface $aptoParameter
     */
    public function __construct(
        RouterInterface $router,
        KernelInterface $kernel,
        RequestStore $requestStore,
        AptoParameterInterface $aptoParameter
    ) {
        parent::__construct($router, $kernel, $requestStore, $aptoParameter);
        $this->basePath = $requestStore->getBasePath();
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @return string
     */
    protected function getPublicFolder(): string
    {
        return $this->basePath;
    }

    /**
     * @return string
     */
    protected function getMediaRelativePath(): string
    {
        return $this->basePath . '/public/media';
    }
}
