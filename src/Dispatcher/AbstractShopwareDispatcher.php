<?php


namespace Deployee\Plugins\ShopwareTasks\Dispatcher;


use Deployee\Plugins\Deploy\Definitions\Tasks\TaskDefinitionInterface;
use Deployee\Plugins\Deploy\Dispatcher\AbstractTaskDefinitionDispatcher;

abstract class AbstractShopwareDispatcher extends AbstractTaskDefinitionDispatcher
{
    protected function getEnvironmentConsoleParameter(TaskDefinitionInterface $taskDefinition): string
    {
        $params = $taskDefinition->define();
        return $params->get('env') !== null
            ? sprintf('--env=%s', $params->get('env'))
            : '';
    }
}