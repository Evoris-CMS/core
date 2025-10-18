<?php

namespace Evoris\Core;

use Evoris\Core\DependecyInjection\PagePass;
use Evoris\Core\Projection\PageProjection;
use Evoris\Core\Listener\RequestListener;
use Evoris\Core\Service\PageRegistry;
use Evoris\Core\Service\PageService;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

class EvorisCoreBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__ . '/Resources/config/services.xml');
    }



    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new PagePass());
    }

    public function configureRoutes(RoutingConfigurator $routes, array $config = [], ContainerBuilder $container = null): void
    {
        $routes
            ->import(__DIR__ . '/Controller', 'attribute')
            ->prefix('/evoris')
            ->namePrefix('evoris_');

        $container->register(RequestListener::class)
            ->setArguments([
                new Reference(PageProjection::class),
                new Reference(PageRegistry::class),
                new Reference(SerializerInterface::class),
                new Reference(Environment::class),
            ])->addTag('kernel.event_listener', [
                'event' => 'kernel.request',
                'priority' => 100,
                'method' => 'onKernelRequest',
            ]);
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('patchlevel_event_sourcing', [
            'aggregates' => [__DIR__ . '/Aggregate'],
            'events' => [__DIR__ . '/Event'],
        ]);

        $builder->prependExtensionConfig('twig', [
            'paths' => [
                __DIR__.'/Resources/templates' => 'evoris',
            ]
        ]);
    }
}