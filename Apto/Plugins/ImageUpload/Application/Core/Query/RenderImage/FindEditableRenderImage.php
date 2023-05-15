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
     * @var string
     */
    private string $renderImageHash;

    /**
     * @param array $state
     * @param string $productId
     * @param string $perspective
     * @param string $renderImageHash
     */
    public function __construct(array $state, string $productId, string $perspective, string $renderImageHash)
    {
        $this->state = $state;
        $this->productId = $productId;
        $this->perspective = $perspective;
        $this->renderImageHash = $renderImageHash;
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
     * @return string
     */
    public function getRenderImageHash(): string
    {
        return $this->renderImageHash;
    }
}
