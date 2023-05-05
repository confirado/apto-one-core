<?php

namespace Apto\Plugins\FloatInputElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\AbstractSpecialElementTranslationExportProvider;
use Apto\Plugins\FloatInputElement\Domain\Core\Model\Product\Element\FloatInputElementDefinition;

class FloatInputTranslationExportProvider extends AbstractSpecialElementTranslationExportProvider
{
    const TRANSLATABLE_FIELDS = ['prefix', 'suffix', 'livePricePrefix', 'livePriceSuffix'];
    const TRANSLATION_TYPE = 'FloatInputElement';
    const DEFINITION_CLASS = FloatInputElementDefinition::class;
}
