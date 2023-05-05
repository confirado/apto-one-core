<?php

namespace Apto\Catalog\Application\Core\Service\RenderImage;

class RenderImageRegistry
{
    /**
     * @var array
     */
    private $renderImageProviders;

    /**
     * @var array
     */
    private $renderImageReducers;

    /**
     * RenderImageCalculatorRegistry constructor.
     */
    public function __construct()
    {
        $this->renderImageProviders = [];
        $this->renderImageReducers = [];
    }

    /**
     * @param RenderImageProvider $renderImageProvider
     */
    public function addRenderImageProvider(RenderImageProvider $renderImageProvider)
    {
        $className = get_class($renderImageProvider);

        if (array_key_exists($className, $this->renderImageProviders)) {
            throw new \InvalidArgumentException('A renderImage provider with an id \'' . $className . '\' is already registered.');
        }

        $this->renderImageProviders[$className] = $renderImageProvider;
    }

    /**
     * @return array
     */
    public function getRenderImageProviders(): array
    {
        return $this->renderImageProviders;
    }

    /**
     * @param RenderImageReducer $renderImageReducer
     */
    public function addRenderImageReducer(RenderImageReducer $renderImageReducer)
    {
        $className = get_class($renderImageReducer);

        if (array_key_exists($className, $this->renderImageProviders)) {
            throw new \InvalidArgumentException('A renderImage reducer with an id \'' . $className . '\' is already registered.');
        }

        $this->renderImageReducers[$className] = $renderImageReducer;
    }

    /**
     * @return array
     */
    public function getRenderImageReducers(): array
    {
        return $this->renderImageReducers;
    }
}