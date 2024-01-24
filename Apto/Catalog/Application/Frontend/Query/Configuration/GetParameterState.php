<?php

namespace Apto\Catalog\Application\Frontend\Query\Configuration;

use Apto\Base\Application\Core\PublicQueryInterface;

class GetParameterState implements PublicQueryInterface
{

    /**
     * @var array
     */
    private $state;

    /**
     * @var array
     */
    private array $parameters;

    /**
     * @param array $state
     * @param array $parameters
     */
    public function __construct(array $state, array $parameters)
    {
        $this->state = $state;
        $this->parameters = $parameters;

        $this->assertValidParameters();
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * we expect it to be like:
     * [
     *    [
     *       'name' => 'quantity',
     *       'value' => 1
     *    ],
     *    [
     *       'name' => 'repetitions',
     *       'value' => 1
     *    ],
     * ];
     *
     * @return void
     */
    private function assertValidParameters(): void
    {
        foreach ($this->parameters as $parameter) {
            if(!array_key_exists('name', $parameter) || !array_key_exists('value', $parameter)) {
                throw new \InvalidArgumentException('Parameters must always have name and value properties!');
            }
        }
    }
}
