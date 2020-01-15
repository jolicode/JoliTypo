<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Bridge\app;

use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use JoliTypo\Bridge\Symfony\JoliTypoBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new TwigBundle(),
            new JoliTypoBundle(),
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');

        if (trait_exists(MailerAssertionsTrait::class)) {
            $loader->load(function (ContainerBuilder $container) {
                $container->loadFromExtension('twig', [
                    'exception_controller' => null,
                ]);
            });
        }
    }

    public function getCacheDir()
    {
        return '/tmp/jolitypo/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return '/tmp/jolitypo/logs/'.$this->environment;
    }
}
