<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Hal\Factory;

use Laminas\ApiTools\Hal\Factory\HalControllerPluginFactory;
use Laminas\ApiTools\Hal\Plugin\Hal as HalPlugin;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class HalControllerPluginFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testInstantiatesHalJsonRenderer(): void
    {
        $viewHelperManager = $this->prophesize(HelperPluginManager::class);
        $viewHelperManager->get('Hal')
            ->willReturn(new HalPlugin(new HydratorPluginManager(new ServiceManager())))
            ->shouldBeCalledTimes(1);

        $services = new ServiceManager();
        $services->setService('ViewHelperManager', $viewHelperManager->reveal());

        $factory = new HalControllerPluginFactory();
        $plugin  = $factory($services, 'Hal');

        self::assertInstanceOf(HalPlugin::class, $plugin);
    }
}
