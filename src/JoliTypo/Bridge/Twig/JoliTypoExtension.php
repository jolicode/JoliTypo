<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Bridge\Twig;

use JoliTypo\Fixer;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class JoliTypoExtension extends AbstractExtension
{
    /**
     * @param array<string, Fixer> $presets The Fixer instances, indexed by preset name
     */
    public function __construct(
        private readonly array $presets = [],
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('jolitypo', $this->translate(...)),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('jolitypo', $this->translate(...), ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function translate(string $text, string $preset = 'default'): string
    {
        if (!isset($this->presets[$preset])) {
            throw new InvalidConfigurationException(\sprintf('There is no "%s" preset configured.', $preset));
        }

        return $this->presets[$preset]->fix($text);
    }
}
