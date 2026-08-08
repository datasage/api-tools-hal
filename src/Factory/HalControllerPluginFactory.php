<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Hal\Factory;

// phpcs:ignore WebimpressCodingStandard.PHP.CorrectClassNameCase.Invalid
use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Hal\Plugin\Hal;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Override;

class HalControllerPluginFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|array $options
     * @return Hal
     */
    #[Override]
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $helpers = $container->get('ViewHelperManager');
        /** @psalm-var Hal */
        return $helpers->get('Hal');
    }

    /**
     * Create service
     *
     * @return Hal
     */
    #[Override]
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            /** @psalm-suppress RedundantConditionGivenDocblockType */
            $serviceLocator = $serviceLocator->getServiceLocator() ?: $serviceLocator;
        }
        /** @psalm-var Hal */
        return $this($serviceLocator, Hal::class);
    }
}
