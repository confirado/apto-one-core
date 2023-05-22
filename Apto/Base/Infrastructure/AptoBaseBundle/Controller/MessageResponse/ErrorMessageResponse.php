<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ErrorMessageResponse extends MessageResponse
{

    /**
     * @var string
     */
    protected $errorType;

    /**
     * @var mixed
     */
    protected $errorPayload = [];

    /**
     * @var null|string
     */
    protected $exceptionUuid;

    /**
     * @var null|string
     */
    protected $exceptionUrl;

    /**
     * Create a new error message from given exception
     * @param string $name
     * @param string $message
     * @param float $duration
     * @param \Exception $e
     * @param string $exceptionUuid
     * @param string $exceptionUrl
     * @return self
     */
    public static function fromException(string $name, string $message, float $duration, \Exception $e, string $exceptionUuid, string $exceptionUrl): self
    {
        $classNameWithNamespace = get_class($e);
        $className = substr($classNameWithNamespace, strrpos($classNameWithNamespace, '\\'));

        return new self(
            $name,
            $message . ' ' . $className . ': ' . $e->getMessage(),
            $duration,
            trim($className, '\\'),
            method_exists($e, 'getPayload') ? $e->getPayload() : [],
            $exceptionUuid,
            $exceptionUrl
        );
    }

    /**
     * @param string $name
     * @param string $message
     * @param float $duration
     * @param string $errorType
     * @param array $errorPayload
     * @param string|null $exceptionUuid
     * @param string|null $exceptionUrl
     */
    public function __construct(string $name, string $message, float $duration, string $errorType = '', array $errorPayload = [], ?string $exceptionUuid = null, ?string $exceptionUrl = null)
    {
        parent::__construct($name, $message, $duration);
        $this->errorType = $errorType;
        $this->errorPayload = $errorPayload;
        $this->exceptionUuid = $exceptionUuid;
        $this->exceptionUrl = $exceptionUrl;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();
        $json['message'] = array_merge($json['message'], [
            'error' => true,
            'errorType' => $this->errorType,
            'errorPayload' => $this->errorPayload
        ]);

        if ($this->exceptionUuid) {
            $json['message']['uuid'] = $this->exceptionUuid;
        }

        if ($this->exceptionUrl) {
            $json['message']['url'] = $this->exceptionUrl;
        }

        return $json;
    }

}
