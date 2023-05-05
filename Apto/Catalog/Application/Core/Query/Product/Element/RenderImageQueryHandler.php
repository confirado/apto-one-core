<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class RenderImageQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ProductElementFinder
     */
    private $productElementFinder;

    /**
     * @var ImageRenderer
     */
    private $imageRenderer;

    /**
     * @var AptoParameterInterface
     */
    private $aptoParameter;

    /**
     * @var RequestStore
     */
    private $requestStore;

    /**
     * @param ProductElementFinder $productElementFinder
     * @param ImageRenderer $imageRenderer
     * @param AptoParameterInterface $aptoParameter
     * @param RequestStore $requestStore
     */
    public function __construct(
        ProductElementFinder $productElementFinder,
        ImageRenderer $imageRenderer,
        AptoParameterInterface $aptoParameter,
        RequestStore $requestStore
    ) {
        $this->productElementFinder = $productElementFinder;
        $this->imageRenderer = $imageRenderer;
        $this->aptoParameter = $aptoParameter;
        $this->requestStore = $requestStore;
    }

    /**
     * @param FindRenderImageByState $query
     * @return string|null
     */
    public function handleFindRenderImageByState(FindRenderImageByState $query): ?string
    {
        $state = new State($query->getState());
        $imageList = $this->productElementFinder->findRenderImagesByState($state, $query->getPerspective());

        $renderImage = $this->imageRenderer->getImageByImageList($imageList, $query->getPerspective(), $state, false, true, $query->getProductId());
        if ($renderImage) {
            return $this->requestStore->getSchemeAndHttpHost() . $renderImage;
        }

        return null;
    }

    /**
     * @param FindRenderImagesByState $query
     * @return array
     */
    public function handleFindRenderImagesByState(FindRenderImagesByState $query): array
    {
        $state = new State($query->getState());
        $renderImages = [];

        foreach ($query->getPerspectives() as $perspective) {
            $imageList = $this->productElementFinder->findRenderImagesByState($state, $perspective);
            $renderImage = $this->imageRenderer->getImageByImageList($imageList, $perspective, $state, false, true, $query->getProductId());
            if ($renderImage) {
                $renderImages[] = [
                    'perspective' => $perspective,
                    'url' => $this->requestStore->getSchemeAndHttpHost() . $renderImage,
                ];
            }
        }

        return $renderImages;
    }

    /**
     * @param FindPerspectivesByState $query
     * @return array
     */
    public function handleFindPerspectivesByState(FindPerspectivesByState $query): array
    {
        $statePerspectives = [];
        $state = new State($query->getState());

        $perspectives = $this->aptoParameter->get('perspectives');

        if (is_array($perspectives) && array_key_exists('perspectives', $perspectives)) {
            $perspectives = $perspectives['perspectives'];

            foreach ($perspectives as $perspective) {
                $imageList = $this->productElementFinder->findRenderImagesByState($state, $perspective);
                if ($this->imageRenderer->hasStatePerspectiveImage($imageList, $perspective, $state, $query->getProductId())) {
                    $statePerspectives[] = $perspective;
                }
            }
        }

        return $statePerspectives;
    }

    public static function getHandledMessages(): iterable
    {
        yield FindRenderImageByState::class => [
            'method' => 'handleFindRenderImageByState',
            'bus' => 'query_bus'
        ];

        yield FindRenderImagesByState::class => [
            'method' => 'handleFindRenderImagesByState',
            'bus' => 'query_bus'
        ];

        yield FindPerspectivesByState::class => [
            'method' => 'handleFindPerspectivesByState',
            'bus' => 'query_bus'
        ];
    }
}
