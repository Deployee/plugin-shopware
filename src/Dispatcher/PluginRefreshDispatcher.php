<?php

namespace Deployee\Plugins\ShopwareTasks\Dispatcher;

use Deployee\Plugins\Deploy\Definitions\Tasks\TaskDefinitionInterface;
use Deployee\Plugins\Deploy\Dispatcher\DispatchResultInterface;
use Deployee\Plugins\ShopwareTasks\Definitions\PluginRefreshDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\ShopwareCommandDefinition;

class PluginRefreshDispatcher extends AbstractShopwareDispatcher
{
    /**
     * @param TaskDefinitionInterface $taskDefinition
     * @return bool
     */
    public function canDispatchTaskDefinition(TaskDefinitionInterface $taskDefinition): bool
    {
        return $taskDefinition instanceof PluginRefreshDefinition;
    }

    /**
     * @param TaskDefinitionInterface $taskDefinition
     * @return DispatchResultInterface
     * @throws \Deployee\Plugins\Deploy\Exception\DispatcherException
     */
    public function dispatch(TaskDefinitionInterface $taskDefinition): DispatchResultInterface
    {
        $params = $taskDefinition->define();
        return $this->delegate(new ShopwareCommandDefinition(
            'plugin:refresh',
            sprintf('-n %s', $this->getEnvironmentConsoleParameter($taskDefinition))
        ));
    }
}