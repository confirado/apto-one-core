<?php

namespace Apto\Base\Domain\Core\Model;

class AptoCustomProperty extends AptoEntity implements \JsonSerializable
{
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
     * @var AptoUuid|null
     */
    protected ?AptoUuid $productConditionId;

    /**
     * @param AptoUuid $id
     * @param string $key
     * @param string $value
     * @param bool $translatable
     * @param AptoUuid|null $productConditionId
     * @throws AptoCustomPropertyException
     */
    public function __construct(AptoUuid $id, string $key, string $value, bool $translatable = false, ?AptoUuid $productConditionId = null)
    {
        if (null == trim($key)) {
            throw new AptoCustomPropertyException('An empty key is not allowed.');
        }
        parent::__construct($id);
        $this->key = $key;
        $this->value = $value;
        $this->translatable = $translatable;
        $this->productConditionId = $productConditionId;
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
     * @return AptoUuid|null
     */
    public function getProductConditionId(): ?AptoUuid
    {
        return $this->productConditionId;
    }

    /**
     * @param AptoUuid|null $productConditionId
     * @return $this
     */
    public function setProductConditionId(?AptoUuid $productConditionId): AptoCustomProperty
    {
        $this->productConditionId = $productConditionId;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->jsonSerialize(),
            'key' => $this->key,
            'value' => $this->value,
            'translatable' => $this->translatable,
            'productConditionId' => $this->productConditionId->jsonSerialize()
        ];
    }

    /**
     * @param AptoUuid $id
     * @return AptoCustomProperty
     * @throws AptoCustomPropertyException
     */
    public function copy(AptoUuid $id): AptoCustomProperty
    {
        return new AptoCustomProperty(
            $id,
            $this->getKey(),
            $this->getValue(),
            $this->getTranslatable(),
            $this->getProductConditionId()
        );
    }
}
