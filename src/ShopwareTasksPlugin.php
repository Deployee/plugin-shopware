<?php

namespace Deployee\Plugins\ShopwareTasks;

use Deployee\Components\Config\ConfigInterface;
use Deployee\Components\Container\ContainerInterface;
use Deployee\Components\Dependency\ContainerResolver;
use Deployee\Components\Environment\EnvironmentInterface;
use Deployee\Components\Persistence\LazyPDO;
use Deployee\Components\Plugins\PluginInterface;
use Deployee\Plugins\Deploy\Dispatcher\DispatcherCollection;
use Deployee\Plugins\Deploy\Helper\TaskCreationHelper;
use Deployee\Plugins\ShellTasks\Helper\ExecutableFinder;
use Deployee\Plugins\ShopwareTasks\Definitions\CacheClearDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\CreateAdminUserDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\GenerateThemeCacheDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\PluginActivateDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\PluginDeactivateDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\PluginInstallDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\PluginRefreshDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\PluginUninstallDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\PluginUpdateDefinition;
use Deployee\Plugins\ShopwareTasks\Definitions\ShopwareCommandDefinition;
use Deployee\Plugins\ShopwareTasks\Dispatcher\CacheClearDispatcher;
use Deployee\Plugins\ShopwareTasks\Dispatcher\CreateAdminUserDispatcher;
use Deployee\Plugins\ShopwareTasks\Dispatcher\GenerateThemeCacheDispatcher;
use Deployee\Plugins\ShopwareTasks\Dispatcher\PluginActivateDispatcher;
use Deployee\Plugins\ShopwareTasks\Dispatcher\PluginDeactivateDispatcher;
use Deployee\Plugins\ShopwareTasks\Dispatcher\PluginInstallDispatcher;
use Deployee\Plugins\ShopwareTasks\Dispatcher\PluginRefreshDispatcher;
use Deployee\Plugins\ShopwareTasks\Dispatcher\PluginUninstallDispatcher;
use Deployee\Plugins\ShopwareTasks\Dispatcher\PluginUpdateDispatcher;
use Deployee\Plugins\ShopwareTasks\Dispatcher\ShopwareCommandDispatcher;
use Deployee\Plugins\ShopwareTasks\Shop\ShopConfig;

class ShopwareTasksPlugin implements PluginInterface
{
    /**
     * @param ContainerInterface $container
     */
    public function boot(ContainerInterface $container)
    {
        /* @var EnvironmentInterface $env */
        $env = $container->get(EnvironmentInterface::class);
        /* @var ConfigInterface $config  */
        $config = $container->get(ConfigInterface::class);
        $path = $config->get('shopware.path', '');
        $path = strpos($path, '/') !== 0 && strpos($path, ':') !== 1
            ? $env->getWorkDir() . DIRECTORY_SEPARATOR . $path
            : $path;
        $container->set('shopware.path', $path);

        $container->set(ShopConfig::class, function (ContainerInterface $container) {
            $envFile = getenv('APP_ENV')
                ? ShopConfig::BASE_ENV_FILE . '.' . getenv('APP_ENV')
                : ShopConfig::BASE_ENV_FILE;

            return new ShopConfig(
                $container->get('shopware.path') . DIRECTORY_SEPARATOR . $envFile
            );
        });

        $container->extend(LazyPDO::class, function (LazyPDO $lazyPDO) use ($container) {
            /* @var ConfigInterface $config */
            $config = $container->get(ConfigInterface::class);

            /* @var ShopConfig $shopConfig */
            $shopConfig = $container->get(ShopConfig::class);

            $config->set('db.type', 'mysql' ?? $shopConfig->get('type'));
            $config->set('db.host', $config->get('db.host') ?? $shopConfig->get('host'));
            $config->set('db.port', $config->get('db.port') ?? $shopConfig->get('port'));
            $config->set('db.database', $config->get('db.database') ?? $shopConfig->get('dbname'));
            $config->set('db.user', $config->get('db.user') ?? $shopConfig->get('username'));
            $config->set('db.password', $config->get('db.password') ?? $shopConfig->get('password'));

            $lazyPDO->changeConnection(
                sprintf(
                    '%s:host=%s;port=%d;dbname=%s',
                    'mysql',
                    $config->get('db.host'),
                    $config->get('db.port'),
                    $config->get('db.database')
                ),
                $config->get('db.user'),
                $config->get('db.password'),
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            );

            return $lazyPDO;
        });
    }

    /**
     * @param ContainerInterface $container
     * @throws \ReflectionException
     */
    public function configure(ContainerInterface $container)
    {
        /* @var ConfigInterface $config */
        $config = $container->get(ConfigInterface::class);

        /* @var ExecutableFinder $execFinder */
        $execFinder = $container->get(ExecutableFinder::class);
        $execFinder->addAlias('swconsole', $container->get('shopware.path') . '/bin/console');

        /* @var TaskCreationHelper $helper */
        $helper = $container->get(TaskCreationHelper::class);
        $helper->addAlias('swCommand', ShopwareCommandDefinition::class);
        $helper->addAlias('swCacheClear', CacheClearDefinition::class);
        $helper->addAlias('swCreateAdmin', CreateAdminUserDefinition::class);
        $helper->addAlias('swGenerateThemeCache', GenerateThemeCacheDefinition::class);
        $helper->addAlias('swPluginInstall', PluginInstallDefinition::class);
        $helper->addAlias('swPluginUninstall', PluginUninstallDefinition::class);
        $helper->addAlias('swPluginUpdate', PluginUpdateDefinition::class);
        $helper->addAlias('swPluginActivate', PluginActivateDefinition::class);
        $helper->addAlias('swPluginDeactivate', PluginDeactivateDefinition::class);
        $helper->addAlias('swPluginRefresh', PluginRefreshDefinition::class);

        /* @var DispatcherCollection $dispatcherCollection */
        $dispatcherCollection = $container->get(DispatcherCollection::class);
        /* @var ContainerResolver $resolver */
        $resolver = $container->get(ContainerResolver::class);

        $dispatcherArray = [
            $resolver->createInstance(ShopwareCommandDispatcher::class),
            $resolver->createInstance(CreateAdminUserDispatcher::class),
            $resolver->createInstance(CacheClearDispatcher::class),
            $resolver->createInstance(GenerateThemeCacheDispatcher::class),
            $resolver->createInstance(PluginInstallDispatcher::class),
            $resolver->createInstance(PluginUninstallDispatcher::class),
            $resolver->createInstance(PluginUpdateDispatcher::class),
            $resolver->createInstance(PluginActivateDispatcher::class),
            $resolver->createInstance(PluginDeactivateDispatcher::class),
            $resolver->createInstance(PluginRefreshDispatcher::class),
        ];

        $dispatcherCollection->addDispatcherArray($dispatcherArray);
    }
}