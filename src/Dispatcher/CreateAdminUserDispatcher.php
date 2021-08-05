<?php

namespace Deployee\Plugins\ShopwareTasks\Dispatcher;

use Deployee\Plugins\Deploy\Definitions\Tasks\TaskDefinitionInterface;
use Deployee\Plugins\Deploy\Dispatcher\AbstractTaskDefinitionDispatcher;
use Deployee\Plugins\Deploy\Dispatcher\DispatchResultInterface;
use Deployee\Plugins\ShopwareTasks\Definitions\CreateAdminUserDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\ShopwareCommandDefinition;

class CreateAdminUserDispatcher extends AbstractTaskDefinitionDispatcher
{
    /**
     * @param TaskDefinitionInterface $taskDefinition
     * @return bool
     */
    public function canDispatchTaskDefinition(TaskDefinitionInterface $taskDefinition): bool
    {
        return $taskDefinition instanceof CreateAdminUserDefinition;
    }

    /**
     * @param TaskDefinitionInterface $taskDefinition
     * @return DispatchResultInterface
     * @throws \Deployee\Plugins\Deploy\Exception\DispatcherException
     */
    public function dispatch(TaskDefinitionInterface $taskDefinition): DispatchResultInterface
    {
        $parameter = $taskDefinition->define();
        return $this->delegate(
            new ShopwareCommandDefinition(
                'user:create',
                sprintf(
                    "--admin --lastName=%s --password=%s --email=%s --firstName=%s",
                    escapeshellarg($parameter->get('username')),
                    escapeshellarg($parameter->get('password')),
                    escapeshellarg($parameter->get('email')),
                    escapeshellarg($parameter->get('name'))
                )
            )
        );
    }

}