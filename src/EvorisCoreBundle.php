<?php

namespace Evoris\Core;

use Patchlevel\EventSourcing\Aggregate\AggregateHeader;
use Patchlevel\EventSourcing\Store\Header\EventIdHeader;
use Patchlevel\EventSourcing\Store\Header\PlayheadHeader;
use Patchlevel\EventSourcing\Store\Header\RecordedOnHeader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\RouteCompilerInterface;

class EvorisCoreBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/Resources/config/services.xml');
    }

    public function configureRoutes(RoutingConfigurator $routes, array $config = [], ContainerBuilder $container = null): void
    {
        $routes
            ->import(__DIR__ . '/Controller', 'attribute')
            ->prefix('/evoris')
            ->namePrefix('evoris_');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('patchlevel_event_sourcing', [
            'aggregates' => [__DIR__ . '/Aggregate'],
            'events' => [__DIR__ . '/Event'],
        ]);
    }
}