<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\ElementDefinitionRegistry;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

use Apto\Base\Domain\Core\Model\InvalidUuidException;

class ElementQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ProductElementFinder
     */
    private $productElementFinder;

    /**
     * @var ElementDefinitionRegistry
     */
    private $elementDefinitionRegistry;

    /**
     * @var AptoJsonSerializer
     */
    protected $aptoJsonSerializer;

    /**
     * ElementQueryHandler constructor.
     * @param ProductElementFinder $productElementFinder
     * @param ElementDefinitionRegistry $elementDefinitionRegistry
     */
    public function __construct(ProductElementFinder $productElementFinder, ElementDefinitionRegistry $elementDefinitionRegistry, AptoJsonSerializer $aptoJsonSerializer)
    {
        $this->productElementFinder = $productElementFinder;
        $this->elementDefinitionRegistry = $elementDefinitionRegistry;
        $this->aptoJsonSerializer = $aptoJsonSerializer;
    }

    /**
     * @param FindElement $query
     * @return array|null
     */
    public function handleFindElement(FindElement $query)
    {
        return $this->productElementFinder->findById($query->getElementId());
    }

    /**
     * @param FindElementPrices $query
     * @return array|null
     */
    public function handleFindElementPrices(FindElementPrices $query)
    {
        return $this->productElementFinder->findPrices($query->getElementId());
    }

    /**
     * @param FindElementPriceFormulas $query
     * @return array|null
     */
    public function handleFindElementPriceFormulas(FindElementPriceFormulas $query)
    {
        return $this->productElementFinder->findPriceFormulas($query->getElementId());
    }

    /**
     * @param FindElementDiscounts $query
     * @return array|null
     */
    public function handleFindElementDiscounts(FindElementDiscounts $query)
    {
        return $this->productElementFinder->findDiscounts($query->getElementId());
    }

    /**
     * @param FindElementRenderImages $query
     * @return array|null
     */
    public function handleFindElementRenderImages(FindElementRenderImages $query)
    {
        return $this->productElementFinder->findRenderImages($query->getElementId());
    }

    /**
     * @param FindElementAttachments $query
     * @return array|null
     */
    public function handleFindElementAttachments(FindElementAttachments $query)
    {
        return $this->productElementFinder->findAttachments($query->getElementId());
    }

    /**
     * @param FindElementGallery $query
     * @return array|null
     */
    public function handleFindElementGallery(FindElementGallery $query)
    {
        return $this->productElementFinder->findGallery($query->getElementId());
    }

    /**
     * @param FindElementCustomProperties $query
     * @return array|null
     */
    public function handleFindElementCustomProperties(FindElementCustomProperties $query)
    {
        return $this->productElementFinder->findCustomProperties($query->getElementId());
    }

    /**
     * @param FindRegisteredElementDefinitions $query
     * @return array
     */
    public function handleFindRegisteredElementDefinitions(FindRegisteredElementDefinitions $query): array
    {
        return $this->elementDefinitionRegistry->jsonSerialize();
    }

    /**
     * @param FindElementComputableValues $query
     * @return array
     * @throws InvalidUuidException
     */
    public function handleFindElementComputableValues(FindElementComputableValues $query): array
    {
        $state = new State($query->getState());
        $sectionId = new AptoUuid($query->getSectionId());
        $elementId = new AptoUuid($query->getElementId());

        $element = $this->productElementFinder->findById($elementId->getId());
        $elementState = $state->getElementState($sectionId, $elementId);

        if (null === $element || null === $elementState || !is_array($elementState)) {
            return [];
        }

        /** @var ElementDefinition $elementDefinition */
        $elementDefinition = $this->aptoJsonSerializer->jsonUnSerialize(json_encode($element['definition'], JSON_UNESCAPED_UNICODE));
        return $elementDefinition->getComputableValues(
            $elementState
        );
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindElement::class => [
            'method' => 'handleFindElement',
            'bus' => 'query_bus'
        ];

        yield FindElementPrices::class => [
            'method' => 'handleFindElementPrices',
            'bus' => 'query_bus'
        ];

        yield FindElementPriceFormulas::class => [
            'method' => 'handleFindElementPriceFormulas',
            'bus' => 'query_bus'
        ];

        yield FindElementDiscounts::class => [
            'method' => 'handleFindElementDiscounts',
            'bus' => 'query_bus'
        ];

        yield FindElementRenderImages::class => [
            'method' => 'handleFindElementRenderImages',
            'bus' => 'query_bus'
        ];

        yield FindElementAttachments::class => [
            'method' => 'handleFindElementAttachments',
            'bus' => 'query_bus'
        ];

        yield FindElementGallery::class => [
            'method' => 'handleFindElementGallery',
            'bus' => 'query_bus'
        ];

        yield FindElementCustomProperties::class => [
            'method' => 'handleFindElementCustomProperties',
            'bus' => 'query_bus'
        ];

        yield FindRegisteredElementDefinitions::class => [
            'method' => 'handleFindRegisteredElementDefinitions',
            'bus' => 'query_bus'
        ];

        yield FindElementComputableValues::class => [
            'method' => 'handleFindElementComputableValues',
            'bus' => 'query_bus'
        ];
    }
}
