<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Hal\Factory;

use Laminas\ApiTools\Hal\Extractor\LinkCollectionExtractor;
use Laminas\ApiTools\Hal\Factory\HalViewHelperFactory;
use Laminas\ApiTools\Hal\Link;
use Laminas\ApiTools\Hal\Metadata\MetadataMap;
use Laminas\ApiTools\Hal\Plugin\Hal as HalPlugin;
use Laminas\ApiTools\Hal\RendererOptions;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function is_array;

class HalViewHelperFactoryTest extends TestCase
{
    use ProphecyTrait;

    /** @var HelperPluginManager */
    private $pluginManager;
    /** @var ServiceManager */
    private $services;

    /**
     * @param array $config
     */
    public function setupPluginManager($config = []): void
    {
        $services = new ServiceManager();

        $services->setService('Laminas\ApiTools\Hal\HalConfig', $config);

        if (isset($config['renderer']) && is_array($config['renderer'])) {
            $rendererOptions = new RendererOptions($config['renderer']);
        } else {
            $rendererOptions = new RendererOptions();
        }
        $services->setService(RendererOptions::class, $rendererOptions);

        $metadataMap = $this->prophesize(MetadataMap::class);
        $metadataMap->getHydratorManager()->willReturn(new HydratorPluginManager($services))->shouldBeCalledTimes(1);
        $services->setService('Laminas\ApiTools\Hal\MetadataMap', $metadataMap->reveal());

        $linkUrlBuilder = $this->createStub(Link\LinkUrlBuilder::class);
        $services->setService(Link\LinkUrlBuilder::class, $linkUrlBuilder);

        $linkCollectionExtractor = $this->createStub(LinkCollectionExtractor::class);
        $services->setService(LinkCollectionExtractor::class, $linkCollectionExtractor);

        // HalViewHelperFactory never fetches ViewHelperManager, and no test asserts on
        // this instance, so a real manager stands in for the double.
        $this->pluginManager = new HelperPluginManager($services);

        $services->setService('ViewHelperManager', $this->pluginManager);

        $this->services = $services;
    }

    public function testInstantiatesHalViewHelper(): void
    {
        $this->setupPluginManager();

        $sharedEventManager = $this->createStub(SharedEventManagerInterface::class);
        $eventManagerMock   = $this->createStub(EventManagerInterface::class);
        $eventManagerMock->method('getSharedManager')->willReturn($sharedEventManager);

        $this->services->setService('EventManager', $eventManagerMock);

        $factory = new HalViewHelperFactory();
        $plugin  = $factory($this->services, HalPlugin::class);

        self::assertInstanceOf(SharedEventManagerInterface::class, $plugin->getEventManager()->getSharedManager());
    }
}
