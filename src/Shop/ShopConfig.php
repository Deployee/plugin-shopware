<?php

namespace Deployee\Plugins\ShopwareTasks\Shop;


class ShopConfig
{
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
        if($this->config === null){
            if(!is_file($this->configFilePath)){
                throw new \InvalidArgumentException("Path to shopware config was not found or is invalid");
            }

            $this->config = require($this->configFilePath);
        }

        return $this->config;
    }
}