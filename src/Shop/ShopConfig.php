<?php

namespace Deployee\Plugins\ShopwareTasks\Shop;

use Symfony\Component\Dotenv\Dotenv;
use Enqueue\Dsn\Dsn;

class ShopConfig
{
    const BASE_ENV_FILE = '.env';
    const APP_ENV = 'APP_ENV';
    const DB_URL = 'DATABASE_URL';

    /**
     * @var string
     */
    private $configFilePath;

    /**
     * @var array
     */
    private $config;

    /**
     * ShopConfig constructor.
     * @param string $configFilePath
     */
    public function __construct(string $configFilePath)
    {
        $this->configFilePath = $configFilePath;
    }

    /**
     * @param string $id
     * @return mixed|null
     */
    public function get(string $id)
    {
        return $this->getConfig()[$id] ?? null;
    }

    /**
     * @return array
     */
    private function getConfig(): array
    {
        if ($this->config === null) {
            if (!is_file($this->configFilePath)) {
                throw new \InvalidArgumentException("Path to shopware config was not found or is invalid");
            }

            $appEnv = getenv(ShopConfig::APP_ENV);
            $dotenv = new Dotenv($appEnv);
            $envs = $dotenv->parse(file_get_contents($this->configFilePath));

            if (!$this->checkDatabaseUrlIsSet($envs))
            {
                throw new \InvalidArgumentException(
                    "env file does not contain the required DATABASE_URL param. env path = " . $this->configFilePath
                );
            }

            $this->config = $this->getDbDataFromEnv($envs);
        }

        return $this->config;
    }

    /**
     * @param array $envs
     * @return bool
     */
    private function checkDatabaseUrlIsSet(array $envs): bool
    {
        return array_key_exists(ShopConfig::DB_URL, $envs);
    }

    /**
     * @param array $envs
     * @return array
     */
    private function getDbDataFromEnv(array $envs): array
    {
        $dsn = Dsn::parse($envs[ShopConfig::DB_URL]);
        $dsn = $dsn[0]->toArray();
        return [
            'username' => $dsn['user'],
            'password' => $dsn['password'],
            'type' => $dsn['scheme'],
            'host' => $dsn['host'],
            'port' => $dsn['port'],
            'dbname' => str_replace('/', '', $dsn['path']),
        ];
    }
}