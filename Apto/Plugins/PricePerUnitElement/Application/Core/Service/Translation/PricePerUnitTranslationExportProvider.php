<?php

namespace Apto\Plugins\PricePerUnitElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\AbstractSpecialElementTranslationExportProvider;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\Product\Element\PricePerUnitElementDefinition;

class PricePerUnitTranslationExportProvider extends AbstractSpecialElementTranslationExportProvider
{
    const TRANSLATABLE_FIELDS = ['textBoxPrefix', 'textBoxSuffix', 'livePricePrefix', 'livePriceSuffix'];
    const TRANSLATION_TYPE = 'PricePerUnitElement';
    const DEFINITION_CLASS = PricePerUnitElementDefinition::class;
}
