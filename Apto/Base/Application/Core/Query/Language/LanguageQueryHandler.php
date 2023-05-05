<?php

namespace Apto\Base\Application\Core\Query\Language;

use Apto\Base\Application\Core\QueryHandlerInterface;

class LanguageQueryHandler implements QueryHandlerInterface
{
    /**
     * @var LanguageFinder
     */
    protected $languageOrmFinder;

    /**
     * FindLanguageHandler constructor.
     * @param LanguageFinder $languageOrmFinder
     */
    public function __construct(LanguageFinder $languageOrmFinder)
    {
        $this->languageOrmFinder = $languageOrmFinder;
    }

    /**
     * @param FindLanguage $query
     * @return array|null
     */
    public function handleFindLanguage(FindLanguage $query)
    {
        return $this->languageOrmFinder->findById($query->getId());
    }

    /**
     * @param FindLanguages $query
     * @return array
     */
    public function handleFindLanguages(FindLanguages $query)
    {
        return $this->languageOrmFinder->findLanguages($query->getSearchString());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindLanguage::class => [
            'method' => 'handleFindLanguage',
            'bus' => 'query_bus'
        ];

        yield FindLanguages::class => [
            'method' => 'handleFindLanguages',
            'bus' => 'query_bus'
        ];
    }
}