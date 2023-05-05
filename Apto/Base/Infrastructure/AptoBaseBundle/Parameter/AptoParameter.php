<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Parameter;

use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Apto\Base\Infrastructure\AptoBaseBundle\HttpFoundation\RequestStore;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AptoParameter implements AptoParameterInterface
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    /**
     * @var string
     */
    private string $host;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param RequestStore $requestStore
     */
    public function __construct(ParameterBagInterface $parameterBag, RequestStore $requestStore)
    {
        $this->parameterBag = $parameterBag;
        $this->host = $requestStore->getHttpHost();
    }

    /**
     * @param string $parameter
     * @return array|bool|float|int|string|null
     */
    public function get(string $parameter)
    {
        // return host parameter
        if (
            $this->parameterBag->has($this->host) &&
            is_array($this->parameterBag->get($this->host)) &&
            array_key_exists($parameter, $this->parameterBag->get($this->host))
        ) {
            return $this->parameterBag->get($this->host)[$parameter];
        }

        // return global parameter
        return $this->parameterBag->get($parameter);
    }

    /**
     * @param string $parameter
     * @return bool
     */
    public function has(string $parameter): bool
    {
        // check if parameter exists in host parameters
        if (
            $this->parameterBag->has($this->host) &&
            is_array($this->parameterBag->get($this->host)) &&
            array_key_exists($parameter, $this->parameterBag->get($this->host))
        ) {
            return true;
        }

        // check if parameter exists in global parameters
        return $this->parameterBag->has($parameter);
    }
}