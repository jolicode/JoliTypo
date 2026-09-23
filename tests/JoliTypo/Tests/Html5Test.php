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

        // The document is returned as a whole. The HTML5 parser drops the whitespace between the doctype and <html>,
        // moves the whitespace found after </body> inside it, and the serializer removes the self-closing slash of <meta>.
        $fixed = <<<'HTML'
            <!DOCTYPE html><html><head>
                <meta charset="UTF-8">
                <title>Coucou</title>
            </head>
            <body>
                “Who Let the Dogs Out?” is a song written and originally recorded by Anslem Douglas (titled “Doggie”).

            </body></html>
            HTML;

        $this->assertSame($fixed, $fixer->fix($html));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/4
     */
    public function testFullPageMarkupKeepsAttributesAndHead(): void
    {
        $fixer = new Fixer([new Fixer\EnglishQuotes(), new Fixer\Ellipsis()]);

        $html = '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>A "quoted" title...</title><link rel="stylesheet" href="a.css"></head><body class="home"><p>Hello "world"...</p></body><!-- after body --></html>';
        $fixed = '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>A "quoted" title...</title><link rel="stylesheet" href="a.css"></head><body class="home"><p>Hello “world”…</p></body><!-- after body --></html>';

        $this->assertSame($fixed, $fixer->fix($html));
    }

    public function testUppercaseMarkup(): void
    {
        $fixer = new Fixer([new Fixer\Ellipsis()]);

        $this->assertSame('<!DOCTYPE html><html><head><title>x</title></head><body><p>a…</p></body></html>', $fixer->fix('<!DOCTYPE HTML><HTML><HEAD><TITLE>x</TITLE></HEAD><BODY><p>a...</p></BODY></HTML>'));
    }

    public function testFragmentKeepsHeadElementsInPlace(): void
    {
        $fixer = new Fixer([new Fixer\EnglishQuotes(), new Fixer\Ellipsis()]);

        $this->assertSame('<style>.a { content: "..." }</style><title>Title</title>Hello “world”… <b>ok</b>', $fixer->fix('<style>.a { content: "..." }</style><title>Title</title>Hello "world"... <b>ok</b>'));
    }

    public function testFragmentWrappedInBodyIsUnwrapped(): void
    {
        $fixer = new Fixer([new Fixer\Ellipsis()]);

        $this->assertSame('<p>Hello…</p>', $fixer->fix('<body><p>Hello...</p></body>'));
    }
}
