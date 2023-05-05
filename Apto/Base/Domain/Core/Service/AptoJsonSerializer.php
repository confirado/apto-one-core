<?php

namespace Apto\Base\Domain\Core\Service;

use Apto\Base\Domain\Core\Model\AptoJsonSerializable;

class AptoJsonSerializer
{
    /**
     * @param AptoJsonSerializable $jsonSerializable
     * @return string|false
     */
    public function jsonSerialize(AptoJsonSerializable $jsonSerializable)
    {
        $jsonEncoded = $jsonSerializable->jsonEncode();
        $this->assertValidJsonEncoded($jsonEncoded);

        return json_encode($jsonEncoded, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $jsonEncoded
     * @return AptoJsonSerializable
     * @throws AptoJsonSerializerException
     */
    public function jsonUnSerialize($jsonEncoded): AptoJsonSerializable
    {
        try {
            $jsonEncoded = json_decode($jsonEncoded, JSON_OBJECT_AS_ARRAY);
        }
        catch (\Exception $e) {
            throw new AptoJsonSerializerException('Cannot convert database value to object due to malformed JSON data.');
        }

        $this->assertValidJsonEncoded($jsonEncoded);

        /** @var AptoJsonSerializable $fullClassName */
        $fullClassName = '\\' . $jsonEncoded['class'];
        return $fullClassName::jsonDecode($jsonEncoded);
    }

    /**
     * @param array $jsonEncoded
     * @return bool
     * @throws AptoJsonSerializerException
     */
    protected function assertValidJsonEncoded(array $jsonEncoded): bool
    {
        if (!isset($jsonEncoded['class'])) {
            throw new AptoJsonSerializerException('Class \'' . $jsonEncoded['class'] . '\' does not exist.');
        }

        if (!array_key_exists('json', $jsonEncoded)) {
            throw new AptoJsonSerializerException('Class \'' . $jsonEncoded['class'] . '\' does not exist.');
        }

        try {
            if (!class_exists($jsonEncoded['class'])) {
                throw new AptoJsonSerializerException('Class \'' . $jsonEncoded['class'] . '\' does not exist.');
            }
        }
        catch (\Exception $exception) {
            throw new AptoJsonSerializerException($exception->getMessage());
        }

        return true;
    }
}
