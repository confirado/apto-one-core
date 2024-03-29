<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Catalog\Domain\Core\Model\Product\Section\Section;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;

class RenderImageOptions implements \JsonSerializable
{
    /**
     * @var array
     */
    private $renderImageOptions;

    /**
     * @var array
     */
    private $offsetOptions;

    /**
     * @param array $renderImageOptions
     * @return RenderImageOptions
     */
    public static function fromArray(array $renderImageOptions): RenderImageOptions
    {
        if(
            !array_key_exists('renderImageOptions', $renderImageOptions) ||
            !array_key_exists('offsetOptions', $renderImageOptions)
        ) {
            throw new InvalidArgumentException('Missing value renderImageOptions or offsetOptions');
        }
        return new self(
            $renderImageOptions['renderImageOptions'],
            $renderImageOptions['offsetOptions']
        );
    }

    /**
     * RenderImageOptions constructor.
     * @param array $renderImageOptions
     * @param array $offsetOptions
     */
    public function __construct(array $renderImageOptions, array $offsetOptions)
    {
        $this->assertValidRenderImageOptions($renderImageOptions);
        $this->assertValidOffsetOptions($offsetOptions);
        $this->renderImageOptions = $renderImageOptions;
        $this->offsetOptions = $offsetOptions;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->renderImageOptions['file'];
    }

    /**
     * @return string
     */
    public function getPerspective(): string
    {
        return $this->renderImageOptions['perspective'];
    }

    /**
     * @return string
     */
    public function getLayer(): string
    {
        return $this->renderImageOptions['layer'];
    }

    /**
     * @return string
     */
    public function getRenderImageType(): string
    {
        return $this->renderImageOptions['type'];
    }

    /**
     * @return string|null
     */
    public function getFormulaHorizontal(): ?string
    {
        return $this->renderImageOptions['formulaHorizontal'];
    }

    /**
     * @return string|null
     */
    public function getFormulaVertical(): ?string
    {
        return $this->renderImageOptions['formulaVertical'];
    }

    /**
     * @return array
     */
    public function getRenderImageValueRefs(): array
    {
        return $this->renderImageOptions['elementValueRefs'];
    }

    /**
     * @return float
     */
    public function getOffsetX(): float
    {
        return $this->offsetOptions['offsetX'];
    }

    /**
     * @return int
     */
    public function getOffsetUnitX(): int
    {
        return $this->offsetOptions['offsetUnitX'];
    }

    /**
     * @return float
     */
    public function getOffsetY(): float
    {
        return $this->offsetOptions['offsetY'];
    }

    /**
     * @return int
     */
    public function getOffsetUnitY(): int
    {
        return $this->offsetOptions['offsetUnitY'];
    }

    /**
     * @return string
     */
    public function getOffsetType(): string
    {
        return $this->offsetOptions['type'];
    }

    /**
     * @return string|null
     */
    public function getFormulaOffsetX(): ?string
    {
        return $this->offsetOptions['formulaOffsetX'];
    }

    /**
     * @return string|null
     */
    public function getFormulaOffsetY(): ?string
    {
        return $this->offsetOptions['formulaOffsetY'];
    }

    /**
     * @return array
     */
    public function getOffsetValueRefs(): array
    {
        return $this->offsetOptions['elementValueRefs'];
    }

    /**
     * @param array $options
     */
    private function assertValidRenderImageOptions(array $options)
    {
        if(
            !array_key_exists('file', $options) ||
            !array_key_exists('layer', $options) ||
            !array_key_exists('perspective', $options) ||
            !array_key_exists('type', $options) ||
            !array_key_exists('formulaHorizontal', $options) ||
            !array_key_exists('formulaVertical', $options) ||
            !array_key_exists('elementValueRefs', $options) ||
            !$options['file'] ||
            !$options['perspective'] ||
            !$options['type']
        ) {
            throw new InvalidArgumentException('Missing value in renderImageOptions');
        }
    }

    /**
     * @param array $options
     */
    private function assertValidOffsetOptions(array $options)
    {
        if(
            !array_key_exists('offsetX', $options) ||
            !array_key_exists('offsetUnitX', $options) ||
            !array_key_exists('offsetY', $options) ||
            !array_key_exists('offsetUnitY', $options) ||
            !array_key_exists('type', $options) ||
            !array_key_exists('formulaOffsetX', $options) ||
            !array_key_exists('formulaOffsetY', $options) ||
            !array_key_exists('elementValueRefs', $options) ||
            !$options['type'] ||
            (!$options['formulaOffsetX'] && (string) $options['formulaOffsetX'] !== '0' && $options['type'] === 'Berechnend') ||
            (!$options['formulaOffsetY'] && (string) $options['formulaOffsetY'] !== '0' && $options['type'] === 'Berechnend')
        ) {
            throw new InvalidArgumentException('Missing value in offsetOptions');
        }
    }

    /**
     * @param Collection $entityMapping
     * @return RenderImageOptions
     */
    public function copy(Collection &$entityMapping): RenderImageOptions
    {
        $renderImageOptions = $this->renderImageOptions;
        $offsetOptions = $this->offsetOptions;

        $renderImageOptions['elementValueRefs'] = $this->copyElementValueRefs($renderImageOptions['elementValueRefs'], $entityMapping);
        $offsetOptions['elementValueRefs'] = $this->copyElementValueRefs($offsetOptions['elementValueRefs'], $entityMapping);

        // set default for offsetUnitX -> backwards compatibility
        if (!array_key_exists('offsetUnitX', $offsetOptions)) {
            $offsetOptions['offsetUnitX'] = RenderImage::OFFSET_UNIT_PERCENT;
        }

        // set default for offsetUnitY -> backwards compatibility
        if (!array_key_exists('offsetUnitY', $offsetOptions)) {
            $offsetOptions['offsetUnitY'] = RenderImage::OFFSET_UNIT_PERCENT;
        }

        return new self(
            $renderImageOptions,
            $offsetOptions
        );
    }

    /**
     * @param array $elementValueRefs
     * @param Collection $entityMapping
     * @return array
     */
    private function copyElementValueRefs(array $elementValueRefs, Collection &$entityMapping): array
    {
        foreach ($elementValueRefs as $key => $elementValueRef) {
            /**
             * @var Section $section
             * @var Section $element
             */
            $section = $entityMapping->get($elementValueRef['sectionId']);
            $element = $entityMapping->get($elementValueRef['elementId']);

            if (null !== $section) {
                $elementValueRefs[$key]['sectionId'] = $section->getId()->getId();
            }

            if (null !== $element) {
                $elementValueRefs[$key]['elementId'] = $element->getId()->getId();
            }
        }

        return $elementValueRefs;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'renderImageOptions' => $this->renderImageOptions,
            'offsetOptions' => $this->offsetOptions
        ];
    }
}
