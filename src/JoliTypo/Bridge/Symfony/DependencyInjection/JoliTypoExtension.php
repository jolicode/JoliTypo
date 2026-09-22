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
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class JoliTypoExtension extends Extension
{
    /**
     * @param array<array<string, mixed>> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var array{presets: array<string, array{locale: ?string, fixers: list<string>}>} $config */
        $config = $this->processConfiguration(new Configuration(), $configs);

        $presets = [];
        foreach ($config['presets'] as $name => $preset) {
            $definition = new Definition(Fixer::class, [$preset['fixers']]);

            if ($preset['locale']) {
                $definition->addMethodCall('setLocale', [$preset['locale']]);
            }

            $id = \sprintf('joli_typo.fixer.%s', $name);
            $container->setDefinition($id, $definition);
            $presets[$name] = new Reference($id);
        }

        $twigExtension = new Definition(JoliTypoTwigExtension::class, [$presets]);
        $twigExtension->addTag('twig.extension');

        $container->setDefinition('joli_typo.twig_extension', $twigExtension);
    }
}
