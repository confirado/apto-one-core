<?php

namespace Apto\Base\Application\Backend\Commands\CustomerGroup;

class UpdateCustomerGroup extends AbstractAddCustomerGroup
{
    /**
     * @var string
     */
    protected $id;

    /**
     * UpdateCustomerGroup constructor.
     * @param string $id
     * @param string $name
     * @param bool $inputGross
     * @param bool $showGross
     * @param string|null $shopId
     * @param string|null $externalId
     * @param bool $fallback
     */
    public function __construct(
        string $id,
        string $name,
        bool $inputGross,
        bool $showGross,
        string $shopId = null,
        string $externalId = null,
        bool $fallback
    ) {
        parent::__construct($name, $inputGross, $showGross, $shopId, $externalId, $fallback);
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}