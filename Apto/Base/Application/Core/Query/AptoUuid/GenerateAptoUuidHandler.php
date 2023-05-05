<?php

namespace Apto\Base\Application\Core\Query\AptoUuid;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Model\AptoUuid;

class GenerateAptoUuidHandler implements QueryHandlerInterface
{
    /**
     * @param GenerateAptoUuid $query
     * @return string|array
     */
    public function handle(GenerateAptoUuid $query)
    {
        if ($query->getNumber() === 1) {
            return $this->nextIdentity();
        }

        $aptoUuid = [];
        for ($i = 0; $i < $query->getNumber(); $i++) {
            $aptoUuid[] = $this->nextIdentity();
        }

        return $aptoUuid;
    }

    /**
     * @return string
     */
    private function nextIdentity()
    {
        $aptoUuid = new AptoUuid();
        return $aptoUuid->getId();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield GenerateAptoUuid::class => [
            'method' => 'handle',
            'bus' => 'query_bus'
        ];
    }
}