<?php


namespace Deployee\Plugins\ShopwareTasks\Definitions;


use Deployee\Plugins\Deploy\Definitions\Parameter\ParameterCollection;
use Deployee\Plugins\Deploy\Definitions\Parameter\ParameterCollectionInterface;

class PluginConfigSetDefinition extends AbstractShopwareDefinition
{
    /**
     * @var string
     */
    protected $plugin;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return ParameterCollectionInterface
     */
    public function define(): ParameterCollectionInterface
    {
        return new ParameterCollection(get_object_vars($this));
    }
}