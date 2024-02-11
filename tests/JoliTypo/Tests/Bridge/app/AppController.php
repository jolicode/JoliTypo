<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Bridge\app;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AppController
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    public function fixAction(): Response
    {
        $template = $this->twig->createTemplate(
            <<<'TWIG'
                <p>Raw content: People's.</p>

                {% apply jolitypo('en') %}
                    <p>Fixed content: People's.</p>
                {% endapply %}
                TWIG
        );

        $content = $this->twig->render($template);

        return new Response($content);
    }
}
