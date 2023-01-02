<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests;

use JoliTypo\Fixer;
use PHPUnit\Framework\TestCase;

class Html5Test extends TestCase
{
    public function testHtml5Markup(): void
    {
        $fixer = new Fixer([new Fixer\Ellipsis()]);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $html5 = <<<'HTML'
            <header>header</header><aside>Dummy content.</aside><nav><ul><li>Content</li></ul></nav><article><video></video><audio></audio><canvas></canvas><figure>Content</figure></article><footer>footer</footer>
            HTML;

        // The test passes if there is no warning about this fix:
        $this->assertSame($html5, $fixer->fix($html5));
    }

    public function testFullPageMarkup(): void
    {
        $fixer = new Fixer([new Fixer\EnglishQuotes()]);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $html = <<<'HTML'
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8" />
                <title>Coucou</title>
            </head>
            <body>
                "Who Let the Dogs Out?" is a song written and originally recorded by Anslem Douglas (titled "Doggie").
            </body>
            </html>
            HTML;

        $fixed = <<<'STRING'
            “Who Let the Dogs Out?” is a song written and originally recorded by Anslem Douglas (titled “Doggie”).
            STRING;

        $this->assertSame($fixed, $fixer->fix($html));
    }
}
