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
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function fixAction()
    {
        if (1 === $this->twig::MAJOR_VERSION) {
            $template = $this->twig->createTemplate(<<<TWIG
<p>Raw content: People's.</p>

<p>{{ "Fixed content: People's."|jolitypo('en') }}</p>
TWIG
            );

            $content = $template->render([]);
        } else {
            $template = $this->twig->createTemplate(<<<TWIG
<p>Raw content: People's.</p>

{% apply jolitypo('en') %}
    <p>Fixed content: People's.</p>
{% endapply %}
TWIG
            );

            $content = $this->twig->render($template);
        }

        return new Response($content);
    }
}
