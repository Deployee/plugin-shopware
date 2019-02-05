<?php


namespace Deployee\Plugins\ShopwareTasks\Definitions;

use Deployee\Plugins\Deploy\Definitions\Tasks\TaskDefinitionInterface;

abstract class AbstractShopwareDefinition implements TaskDefinitionInterface
{
    /**
     * @var string
     */
    protected $env = 'production';

    /**
     * @param string $env
     * @return $this
     */
    public function env(string $env)
    {
        $this->env = $env;
        return $this;
    }
}