<?php


namespace Deployee\Plugins\ShopwareTasks\Shop;


use PHPUnit\Framework\TestCase;

class ShopConfigTest extends TestCase
{
    public function testGet()
    {
        $shopConfig = new ShopConfig(__DIR__ . '/test_config.php');

        $this->assertSame('bar', $shopConfig->get('foo'));
        $this->assertSame(['foo1', 'foo2'], $shopConfig->get('bar'));
        $this->assertNull($shopConfig->get('foobar'));
    }

    public function testGetFail()
    {
        $shopConfig = new ShopConfig('/file/dies/not/exist.php');
        $this->expectException(\InvalidArgumentException::class);
        $shopConfig->get('foo');
    }
}