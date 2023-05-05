<?php

namespace Apto\Catalog\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslationTypeNotFoundException;
use Apto\Catalog\Application\Core\Query\Group\GroupFinder;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Application\Core\Service\Translation\AbstractTranslationExportProvider;

class GroupTranslationExportProvider extends AbstractTranslationExportProvider
{
    const TRANSLATION_TYPE = 'Group';

    /**
     * @var GroupFinder
     */
    private $groupFinder;

    /**
     * @param GroupFinder $groupFinder
     * @throws TranslationTypeNotFoundException
     */
    public function __construct(GroupFinder $groupFinder)
    {
        parent::__construct();
        $this->groupFinder = $groupFinder;
    }

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getTranslatedValues(): array
    {
        $translatedValues = $this->groupFinder->findGroups();
        $translatedValueImportExportObjects = [];
        $i = 1;

        foreach ($translatedValues['data'] as $translatedValue) {
            $ident = 'Group#' . $i . '_';
            $translatedValueImportExportObjects[] = $this->makeTranslationItem($ident . 'name', $translatedValue['name'], $translatedValue['id']);
            $i++;
        }
        return $translatedValueImportExportObjects;
    }
}