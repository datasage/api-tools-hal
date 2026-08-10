<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Hal\Factory;

// phpcs:ignore WebimpressCodingStandard.PHP.CorrectClassNameCase.Invalid
use Laminas\ApiTools\Hal\Plugin\Hal;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Override;
use Psr\Container\ContainerInterface;

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
}
