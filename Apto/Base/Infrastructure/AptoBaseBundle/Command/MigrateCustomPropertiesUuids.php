<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\AptoCustomProperty;
use Apto\Base\Application\Core\Query\AptoCustomProperty\AptoCustomPropertyFinder;

class MigrateCustomPropertiesUuids extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'apto:migrate:custom-properties-uuids';

    /**
     * @var AptoCustomPropertyFinder
     */
    private AptoCustomPropertyFinder $aptoCustomPropertyFinder;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @param AptoCustomPropertyFinder $aptoCustomPropertyFinder
     * @param ManagerRegistry $doctrine
     * @param string|null $name
     */
    public function __construct(
        AptoCustomPropertyFinder $aptoCustomPropertyFinder,
        ManagerRegistry $doctrine,
        string $name = null
    ) {
        parent::__construct($name);
        $this->aptoCustomPropertyFinder = $aptoCustomPropertyFinder;
        $this->doctrine = $doctrine;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Use this command to add uuids to custom properties if you update from 2.1 to 2.2.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityManager = $this->doctrine->getManager();
        $customProperties = $this->aptoCustomPropertyFinder->findCustomProperties();

        foreach ($customProperties['data'] as $customProperty) {
            // update only custom properties who don't have an uuid yet
            if ('' !== trim($customProperty['id'])) {
                continue;
            }

            /** @var QueryBuilder $queryBuilder */
            $output->writeln(['Update CustomProperty ' . $customProperty['key'] . '|' . $customProperty['surrogateId']]);
            $queryBuilder = $entityManager->createQueryBuilder();
            $queryBuilder
                ->update(AptoCustomProperty::class, 'AptoCustomProperty')
                ->set('AptoCustomProperty.id.id', '?1')
                ->where('AptoCustomProperty.surrogateId = ?2')
                ->setParameters(new ArrayCollection([
                    new Parameter('1', (new AptoUuid())->getId()),
                    new Parameter('2', $customProperty['surrogateId'])
                ]))
            ;

            $queryBuilder->getQuery()->execute();
        }

        return Command::SUCCESS;
    }
}
