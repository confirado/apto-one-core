<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus;

use Apto\Base\Infrastructure\AptoBaseBundle\Security\Acl\AclManager;

class MessageBusFirewall
{
    /**
     * @var array
     */
    private $commandRules;

    /**
     * @var array
     */
    private $queryRules;

    /**
     * @var CommandBusFirewallRule
     */
    private $defaultCommandRule;

    /**
     * @var QueryBusFirewallRule
     */
    private $defaultQueryRule;

    /**
     * @param AclManager $aclManager
     */
    public function __construct(AclManager $aclManager)
    {
        $this->commandRules = [];
        $this->queryRules = [];
        $this->defaultCommandRule = new DefaultCommandRule($aclManager);
        $this->defaultQueryRule = new DefaultQueryRule($aclManager);
    }

    /**
     * @param CommandBusFirewallRule $commandRule
     * @param string $commandClass
     */
    public function addCommandRule(CommandBusFirewallRule $commandRule, string $commandClass)
    {
        if (isset($this->commandRules[$commandClass])) {
            throw new \InvalidArgumentException('ClassName \' ' . $commandClass . ' \' is already registered. You cannot register a command twice');
        }
        $this->commandRules[$commandClass] = $commandRule;
    }

    /**
     * @param QueryBusFirewallRule $queryRule
     * @param string $queryClass
     */
    public function addQueryRule(QueryBusFirewallRule $queryRule, string $queryClass)
    {
        if (isset($this->queryRules[$queryClass])) {
            throw new \InvalidArgumentException('ClassName \' ' . $queryClass . ' \' is already registered. You cannot register a query twice');
        }
        $this->queryRules[$queryClass] = $queryRule;
    }

    /**
     * @param $commandClass
     * @return CommandBusFirewallRule
     */
    private function getCommandRule($commandClass): CommandBusFirewallRule
    {
        if(isset($this->commandRules[$commandClass])) {
            return $this->commandRules[$commandClass];
        }
        return $this->defaultCommandRule;
    }

    /**
     * @param $queryClass
     * @return QueryBusFirewallRule
     */
    private function getQueryRule($queryClass): QueryBusFirewallRule
    {
        if(isset($this->queryRules[$queryClass])) {
            return $this->queryRules[$queryClass];
        }
        return $this->defaultQueryRule;
    }

    /**
     * @param string $commandClass
     * @return bool
     */
    public function commandGranted(string $commandClass): bool
    {
        $rule = $this->getCommandRule($commandClass);
        return $this->handleGranted($rule, $commandClass);
    }

    /**
     * @param string $queryClass
     * @return bool
     */
    public function queryGranted(string $queryClass): bool
    {
        $rule = $this->getQueryRule($queryClass);
        return $this->handleGranted($rule, $queryClass);
    }

    /**
     * @param MessageBusFirewallRule $rule
     * @param string $messageClass
     * @return bool
     */
    private function handleGranted(MessageBusFirewallRule $rule, string $messageClass): bool
    {
        return $rule->isGranted($messageClass);
    }

    /**
     * @param object $command
     * @return bool
     */
    public function commandExecutionAllowed($command): bool
    {
        $rule = $this->getCommandRule(get_class($command));
        return $this->handleExecution($rule, $command);
    }

    /**
     * @param $query
     * @return bool
     */
    public function queryExecutionAllowed($query): bool
    {
        $rule = $this->getQueryRule(get_class($query));
        return $this->handleExecution($rule, $query);
    }

    /**
     * @param MessageBusFirewallRule $rule
     * @param $message
     * @return bool
     */
    private function handleExecution(MessageBusFirewallRule $rule, $message): bool
    {
        return $rule->isExecutionAllowed($message);
    }
}