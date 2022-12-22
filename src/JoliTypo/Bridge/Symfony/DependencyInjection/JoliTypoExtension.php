<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Bridge\Symfony\DependencyInjection;

use JoliTypo\Bridge\Twig\JoliTypoExtension as JoliTypoTwigExtension;
use JoliTypo\Fixer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JoliTypoExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $presets = $this->createPresetDefinition($container, $config);

        // Twig extension
        $twigExtension = new Definition(JoliTypoTwigExtension::class);
        $twigExtension->addTag('twig.extension');
        $twigExtension->setArguments([$presets]);

        $container->setDefinition('joli_typo.twig_extension', $twigExtension);
    }

    private function createPresetDefinition(ContainerBuilder $container, array $config): array
    {
        $presets = [];

        foreach ($config['presets'] as $name => $preset) {
            $definition = new Definition(Fixer::class);

            if ($preset['locale']) {
                $definition->addMethodCall('setLocale', [$preset['locale']]);
            }

            $fixers = [];
            foreach ($preset['fixers'] as $fixer) {
                // Allow to use services as fixer?
                $fixers[] = $fixer;
            }

            $definition->addArgument($fixers);
            $container->setDefinition(sprintf('joli_typo.fixer.%s', $name), $definition);

            $presets[$name] = new Reference(sprintf('joli_typo.fixer.%s', $name));
        }

        return $presets;
    }
}
