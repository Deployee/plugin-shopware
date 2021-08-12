<?php

namespace Deployee\Plugins\ShopwareTasks\Dispatcher;

use Deployee\Plugins\Deploy\Definitions\Tasks\TaskDefinitionInterface;
use Deployee\Plugins\Deploy\Dispatcher\DispatchResultInterface;
use Deployee\Plugins\ShopwareTasks\Definitions\PluginConfigSetDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\ShopwareCommandDefinition;

class PluginConfigSetDispatcher extends AbstractShopwareDispatcher
{
    /**
     * @param TaskDefinitionInterface $taskDefinition
     * @return bool
     */
    public function canDispatchTaskDefinition(TaskDefinitionInterface $taskDefinition): bool
    {
        return $taskDefinition instanceof PluginConfigSetDefinition;
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
            'system:config:set',
            sprintf(
                '-n %s %s %s',
                escapeshellarg($params->get('key')),
                escapeshellarg($params->get('value')),
                $this->getEnvironmentConsoleParameter($taskDefinition)
            )
        ));
    }
}