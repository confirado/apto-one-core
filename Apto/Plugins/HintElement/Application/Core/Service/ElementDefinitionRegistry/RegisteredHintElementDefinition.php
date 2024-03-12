<?php

namespace Apto\Plugins\HintElement\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\HintElement\Domain\Core\Model\Product\Element\HintElementDefinition;

class RegisteredHintElementDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return HintElementDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return HintElementDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return HintElementDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return HintElementDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
		if (!isset($definitionValues['link'])) {
			$definitionValues['link'] = '';
		}

		if (!isset($definitionValues['buttonText'])) {
			$definitionValues['buttonText'] = new AptoTranslatedValue([]);
		} else {
			$definitionValues['buttonText'] = AptoTranslatedValue::fromArray($definitionValues['buttonText']);
		}

		if (!isset($definitionValues['openLinkInNewTab'])) {
			$definitionValues['openLinkInNewTab'] = '_blank';
		}

		if (!isset($definitionValues['active'])) {
			$definitionValues['active'] = false;
		}

		return new HintElementDefinition(
			$definitionValues['link'],
			$definitionValues['buttonText'],
			$definitionValues['openLinkInNewTab'],
			$definitionValues['active']
		);
    }
}
