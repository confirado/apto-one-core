<?php

namespace Apto\Plugins\HintElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

class HintElementDefinition implements ElementDefinition
{
    const NAME = 'Hinweis Element';
    const BACKEND_COMPONENT = '<hint-element-definition definition-validation="setDefinitionValidation(definitionValidation)"></hint-element-definition>';
    const FRONTEND_COMPONENT = '<hint-element-definition section-ctrl="$ctrl.section" section="section" element="element"></hint-element-definition>';

	/**
	 * @var string
	 */
	protected $link;

	/**
	 * @var AptoTranslatedValue
	 */
	protected $buttonText;

	/**
	 * @var string
	 */
	protected $openLinkInNewTab;

	/**
	 * @var bool
	 */
	protected $active;

    /**
     * HintElementDefinition constructor.
     */
    public function __construct(string $link, AptoTranslatedValue $buttonText, string $openLinkInNewTab, bool $active)
    {
		$this->link = $link;
		$this->buttonText = $buttonText;
		$this->openLinkInNewTab = $openLinkInNewTab;
		$this->active = $active;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        return [];
    }

    /**
     * @param array $selectedValues
     * @return mixed|null
     */
    public function getComputableValues(array $selectedValues): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getStaticValues(): array
    {
        return [
            'aptoElementDefinitionId' => 'apto-element-hint-element',
			'link' => $this->link,
			'buttonText' => $this->buttonText,
			'openLinkInNewTab' => $this->openLinkInNewTab,
			'active' => $this->active,
		];
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        return [];
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public static function getBackendComponent(): string
    {
        return self::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public static function getFrontendComponent(): string
    {
        return self::FRONTEND_COMPONENT;
    }

    /**
     * @return array
     */
    public function jsonEncode(): array
    {
        return [
            'class' => get_class($this),
			'json' => [
				'link' => $this->link,
				'buttonText' => $this->buttonText->jsonSerialize(),
				'openLinkInNewTab' => $this->openLinkInNewTab,
				'active' => $this->active,
			]
        ];
    }

    /**
     * @param array $json
     * @return ElementDefinition
     */
    public static function jsonDecode(array $json): ElementDefinition
    {
		if (self::class !==  $json['class']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'HintElementDefinition\' due to wrong class namespace.');
        }

		if (!array_key_exists('link', $json['json'])) {
			$json['json']['link'] = '';
		}

		if (!array_key_exists('buttonText', $json['json'])) {
			$json['json']['buttonText'] = new AptoTranslatedValue([]);
		} else {
			$json['json']['buttonText'] = AptoTranslatedValue::fromArray($json['json']['buttonText']);
		}

		if (!array_key_exists('openLinkInNewTab', $json['json'])) {
			$json['json']['openLinkInNewTab'] = '_blank';
		}

		if (!array_key_exists('active', $json['json'])) {
			$json['json']['active'] = false;
		}

        return new self(
			$json['json']['link'],
			$json['json']['buttonText'],
			$json['json']['openLinkInNewTab'],
			$json['json']['active']
		);
    }
}
