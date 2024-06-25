<?php

namespace Apto\Catalog\Domain\Core\Service\StateValidation;

class InvalidStateException extends StateException
{
    /**
     * @param string $message
     * @param string $product
     * @param string $section
     * @param string|null $element
     * @param string|null $property
     * @param mixed|null $value
     * @param array|null $errorMessage
     */
    public function __construct(string $message, string $product, string $section, ?string $element = null, ?string $property = null, $value = null, ?array $errorMessage = null)
    {
        parent::__construct($message);

        $this->payload = [
            'product' => $product,
            'section' => $section
        ];

        if (null !== $element) {
            $this->payload['element'] = $element;
        }

        if (null !== $property) {
            $this->payload['property'] = $property;
        }

        if (null !== $value) {
            $this->payload['value'] = $value;
        }

        if (null !== $errorMessage) {
            $this->payload['errorMessage'] = $errorMessage;
        }
    }
}
