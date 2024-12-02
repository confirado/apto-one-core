<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

class AddPartCustomProperty extends PartChildCommand
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $translatable;

    /**
     * @param string $partId
     * @param string $key
     * @param string $value
     * @param bool $translatable
     */
    public function __construct(string $partId, string $key, string $value, bool $translatable = false)
    {
        parent::__construct($partId);
        $this->key = $key;
        $this->value = $translatable ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
        $this->translatable = $translatable;
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
     * @return bool
     */
    public function getTranslatable(): bool
    {
        return $this->translatable;
    }
}
