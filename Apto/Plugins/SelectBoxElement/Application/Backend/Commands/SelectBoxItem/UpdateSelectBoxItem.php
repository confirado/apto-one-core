<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Commands\SelectBoxItem;

class UpdateSelectBoxItem extends AbstractAddSelectBoxItem
{
    /**
     * @var string
     */
    protected $id;

    /**
     * UpdateSelectBoxItem constructor.
     * @param string $id
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param array $name
     */
    public function __construct(string $id, string $productId, string $sectionId, string $elementId, array $name)
    {
        parent::__construct(
            $productId,
            $sectionId,
            $elementId,
            $name
        );
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