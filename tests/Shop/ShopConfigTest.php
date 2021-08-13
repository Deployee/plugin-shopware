<?php


namespace Deployee\Plugins\ShopwareTasks\Shop;


use PHPUnit\Framework\TestCase;

class ShopConfigTest extends TestCase
{
    public function testGet()
    {
        $shopConfig = new ShopConfig(__DIR__ . '/.env');

        $this->assertSame('mysql', $shopConfig->get('type'));
        $this->assertSame('fooBarHost', $shopConfig->get('host'));
        $this->assertSame(3306, $shopConfig->get('port'));
        $this->assertSame('db', $shopConfig->get('dbname'));
        $this->assertSame('foo', $shopConfig->get('username'));
        $this->assertSame('bar', $shopConfig->get('password'));

        $this->assertNull($shopConfig->get('foobar'));
    }

    public function testGetFail()
    {
        $shopConfig = new ShopConfig('/file/dies/not/exist.php');
        $this->expectException(\InvalidArgumentException::class);
        $shopConfig->get('foo');
    }

    public function testGetFail2()
    {
        $shopConfig = new ShopConfig(__DIR__ . '/.empty_env');
        $this->expectException(\InvalidArgumentException::class);
        $shopConfig->get('foo');
    }
}