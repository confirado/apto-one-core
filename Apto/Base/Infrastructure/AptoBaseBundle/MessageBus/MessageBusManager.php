<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus;

use Apto\Base\Application\Core\CommandInterface;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\Exception\CommandNotSupported;
use ReflectionException;
use Apto\Base\Application\Core\Commands\UploadCommand;
use Apto\Base\Application\Core\QueryInterface;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\Exception\CommandNotFoundException;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\Exception\QueryNotFoundException;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\Exception\ClassNotFoundException;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\Exception\QueryNotSupported;

/**
 * Class MessageBusManager
 * @package Apto\Base\Infrastructure\AptoBaseBundle\MessageBus
 */
class MessageBusManager
{
    /**
     * @var array
     */
    protected $commands = [];

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @param array $commands
     */
    public function addCommands(array $commands)
    {
        $this->commands = array_merge($this->commands, $commands);
    }

    /**
     * @param array $queries
     */
    public function addQueries(array $queries)
    {
        $this->queries = array_merge($this->queries, $queries);
    }

    /**
     * @return array
     */
    public function getRegisteredCommands()
    {
        return $this->commands;
    }

    /**
     * @return array
     */
    public function getRegisteredQueries()
    {
        return $this->queries;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @param array $files
     * @return CommandInterface
     * @throws ClassNotFoundException
     * @throws CommandNotFoundException
     * @throws CommandNotSupported
     * @throws ReflectionException
     */
    public function getCommand(string $name, array $arguments, array $files = []): CommandInterface
    {
        $commandClass = $this->getCommandClass($name);

        $reflection = new \ReflectionClass($commandClass);
        $instance = $reflection->newInstanceArgs($arguments);

        if (!($instance instanceof CommandInterface)) {
            throw new CommandNotSupported('Command has to implement CommandInterface.');
        }

        if ($instance instanceof UploadCommand) {
            $instance->setFiles($files);
        }

        return $instance;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return QueryInterface
     * @throws ClassNotFoundException
     * @throws QueryNotFoundException
     * @throws QueryNotSupported
     * @throws ReflectionException
     */
    public function getQuery(string $name, array $arguments): QueryInterface
    {
        $queryClass = $this->getQueryClass($name);

        $reflection = new \ReflectionClass($queryClass);
        $instance = $reflection->newInstanceArgs($arguments);

        if (!($instance instanceof QueryInterface)) {
            throw new QueryNotSupported('Query has to implement QueryInterface.');
        }

        return $instance;
    }

    /**
     * @param string $name
     * @return string
     * @throws ClassNotFoundException
     * @throws CommandNotFoundException
     */
    public function getCommandClass(string $name): string
    {
        if(!isset($this->commands[$name])) {
            throw new CommandNotFoundException('The command \'' . $name . '\' was not found');
        }

        if(!class_exists($this->commands[$name])) {
            throw new ClassNotFoundException('The class \'' . $this->commands[$name] . '\' was not found');
        }

        return $this->commands[$name];
    }

    /**
     * @param string $name
     * @return string
     * @throws ClassNotFoundException
     * @throws QueryNotFoundException
     */
    public function getQueryClass(string $name): string
    {
        if(!isset($this->queries[$name])) {
            throw new QueryNotFoundException('The query \'' . $name . '\' was not found');
        }

        if(!class_exists($this->queries[$name])) {
            throw new ClassNotFoundException('The class \'' . $this->queries[$name] . '\' was not found');
        }

        return $this->queries[$name];
    }
}