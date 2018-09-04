<?php

namespace JoliTypo\Bridge\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JoliTypoExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $presets = $this->createPresetDefinition($container, $config);

        // Twig extension
        $twig_extension = new Definition('JoliTypo\Bridge\Twig\JoliTypoExtension');
        $twig_extension->addTag('twig.extension');
        $twig_extension->setArguments(array($presets));

        $container->setDefinition('joli_typo.twig_extension', $twig_extension);
    }

    private function createPresetDefinition(ContainerBuilder $container, $config)
    {
        $presets = array();

        foreach ($config['presets'] as $name => $preset) {
            $definition = new Definition('JoliTypo\Fixer');

            if ($preset['locale']) {
                $definition->addMethodCall('setLocale', array($preset['locale']));
            }

            $fixers = array();
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
