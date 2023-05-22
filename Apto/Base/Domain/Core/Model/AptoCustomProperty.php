<?php

namespace Apto\Base\Domain\Core\Model;

class AptoCustomProperty implements \JsonSerializable
{

    /**
     * @var int
     */
    protected $surrogateId;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var bool
     */
    protected $translatable;

    /**
     * AptoCustomProperty constructor.
     * @param string $key
     * @param string $value
     * @param bool $translatable
     * @throws AptoCustomPropertyException
     */
    public function __construct(string $key, string $value, bool $translatable = false)
    {
        if (null == trim($key)) {
            throw new AptoCustomPropertyException('An empty key is not allowed.');
        }
        $this->key = $key;
        $this->value = $value;
        $this->translatable = $translatable;
    }

    /**
     * @return int
     */
    public function getSurrogateId(): int
    {
        return $this->surrogateId;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return AptoCustomProperty
     */
    public function setValue(string $value): AptoCustomProperty
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function getTranslatable(): bool
    {
        return $this->translatable;
    }

    /**
     * @param bool $translatable
     * @return AptoCustomProperty
     */
    public function setTranslatable(bool $translatable): AptoCustomProperty
    {
        $this->translatable = $translatable;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'translatable' => $this->translatable
        ];
    }

    /**
     * @return AptoCustomProperty
     * @throws AptoCustomPropertyException
     */
    public function copy(): AptoCustomProperty
    {
        return new AptoCustomProperty(
            $this->getKey(),
            $this->getValue(),
            $this->getTranslatable()
        );
    }
}
