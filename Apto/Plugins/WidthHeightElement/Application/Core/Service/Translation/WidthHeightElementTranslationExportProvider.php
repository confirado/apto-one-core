<?php

namespace Apto\Plugins\WidthHeightElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\AbstractSpecialElementTranslationExportProvider;
use Apto\Plugins\WidthHeightElement\Domain\Core\Model\Product\Element\WidthHeightElementDefinition;

class WidthHeightElementTranslationExportProvider extends AbstractSpecialElementTranslationExportProvider
{
    const TRANSLATABLE_FIELDS = ['prefixWidth', 'prefixHeight', 'suffixWidth', 'suffixHeight'];
    const TRANSLATION_TYPE = 'WidthHeightElement';
    const DEFINITION_CLASS = WidthHeightElementDefinition::class;
}
