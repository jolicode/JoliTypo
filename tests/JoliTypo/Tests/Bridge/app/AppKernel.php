<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Bridge\app;

use JoliTypo\Bridge\Symfony\JoliTypoBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new JoliTypoBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');

        // Set framework.router.utf8 to avoid deprecated error on SF 5.1
        if (version_compare(self::VERSION, '5.0', 'gt')) {
            $loader->load(function (ContainerBuilder $container) {
                $container->loadFromExtension('framework', [
                    'router' => [
                        'utf8' => true,
                    ],
                ]);
            });
        }

        if (trait_exists(MailerAssertionsTrait::class)) {
            $loader->load(function (ContainerBuilder $container) {
                $container->loadFromExtension('twig', [
                    'exception_controller' => null,
                ]);
            });
        }
    }

    public function getCacheDir(): string
    {
        return '/tmp/jolitypo/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return '/tmp/jolitypo/logs/' . $this->environment;
    }
}
