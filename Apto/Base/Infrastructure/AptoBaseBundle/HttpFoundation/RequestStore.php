<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Apto\Base\Application\Core\Service\RequestStore as RequestStoreInterface;

class RequestStore implements RequestStoreInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var Request|null
     */
    protected $currentRequest;

    /**
     * RequestStore constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->currentRequest = $this->requestStack->getCurrentRequest();
    }

    /**
     * @return string
     */
    public function getSchemeAndHttpHost(): string
    {
        if (null === $this->currentRequest) {
            return '';
        }

        return $this->currentRequest->getSchemeAndHttpHost();
    }

    /**
     * @return string
     */
    public function getHttpHost(): string
    {
        if (null === $this->currentRequest) {
            return '';
        }

        return $this->currentRequest->getHttpHost();
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        if (null === $this->currentRequest) {
            return '';
        }

        return $this->currentRequest->getLocale();
    }

    /**
     * @return string
     */
    public function getDefaultLocale(): string
    {
        if (null === $this->currentRequest) {
            return '';
        }

        return $this->currentRequest->getDefaultLocale();
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->currentRequest->getBasePath();
    }
}
