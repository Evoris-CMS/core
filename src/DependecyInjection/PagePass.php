<?php

namespace Evoris\Core\DependecyInjection;

use Evoris\Core\Atrribute\Page;
use Evoris\Core\Service\PageRegistry;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PagePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $pages = [];

        foreach ($container->getDefinitions() as $id => $def) {
            if($def->isAbstract() || !$def->getClass())
            {
                continue;
            }

            $class = $container->getParameterBag()->resolveValue($def->getClass());

            if(!class_exists($class)) {
                continue;
            }

            $ref = new \ReflectionClass($class);
            $attributes = $ref->getAttributes(Page::class);

            if(count($attributes) === 0) {
                continue;
            }

            /** @var Page $attr */
            $attr = $attributes[0]->newInstance();

            $pages[$attr->name] = ['class' => $class, 'template' => $attr->template];
        }

        if(!$container->hasDefinition(PageRegistry::class)) {
            return;
        }

        $def = $container->getDefinition(PageRegistry::class);
        $def->setArgument('$pages', $pages);
    }
}