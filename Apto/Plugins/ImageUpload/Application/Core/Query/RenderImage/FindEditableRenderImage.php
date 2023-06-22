<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Query\RenderImage;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindEditableRenderImage implements PublicQueryInterface
{
    /**
     * @var array
     */
    protected array $state;

    /**
     * @var string
     */
    protected string $productId;

    /**
     * @var string
     */
    private string $perspective;

    /**
     * @var array
     */
    private array $renderImageIds;

    /**
     * @param array $state
     * @param string $productId
     * @param string $perspective
     * @param array $renderImageIds
     */
    public function __construct(array $state, string $productId, string $perspective, array $renderImageIds)
    {
        $this->state = $state;
        $this->productId = $productId;
        $this->perspective = $perspective;
        $this->renderImageIds = $renderImageIds;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getPerspective(): string
    {
        return $this->perspective;
    }

    /**
     * @return array
     */
    public function getRenderImageIds(): array
    {
        return $this->renderImageIds;
    }
}
