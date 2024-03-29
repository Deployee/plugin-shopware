<?php

namespace Deployee\Plugins\ShopwareTasks\Dispatcher;

use Deployee\Plugins\Deploy\Definitions\Tasks\TaskDefinitionInterface;
use Deployee\Plugins\Deploy\Dispatcher\DispatchResultInterface;
use Deployee\Plugins\ShopwareTasks\Definitions\CacheClearDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\ShopwareCommandDefinition;

class CacheClearDispatcher extends AbstractShopwareDispatcher
{
    /**
     * @param TaskDefinitionInterface $taskDefinition
     * @return bool
     */
    public function canDispatchTaskDefinition(TaskDefinitionInterface $taskDefinition): bool
    {
        return $taskDefinition instanceof CacheClearDefinition;
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
            'cache:clear',
            sprintf('-n %s', $this->getEnvironmentConsoleParameter($taskDefinition))
        ));
    }
}