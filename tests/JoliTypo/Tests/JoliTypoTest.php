<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests;

use JoliTypo\Exception\BadRuleSetException;
use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;
use PHPUnit\Framework\TestCase;

class JoliTypoTest extends TestCase
{
    public function testSimpleInstance(): void
    {
        $fixer = new Fixer(['Ellipsis']);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertSame('Coucou&hellip;', $fixer->fix('Coucou...'));
    }

    public function testSimpleInstanceRulesChange(): void
    {
        $fixer = new Fixer(['Ellipsis']);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertSame('Coucou&hellip;', $fixer->fix('Coucou...'));

        $fixer->setRules(['CurlyQuote']);

        $this->assertSame('I&rsquo;m a pony.', $fixer->fix("I'm a pony."));
    }

    public function testHtmlComments(): void
    {
        $fixer = new Fixer(['Ellipsis']);
        $this->assertSame('<p>Coucou&hellip;</p> <!-- Not Coucou... -->', $fixer->fix('<p>Coucou...</p> <!-- Not Coucou... -->'));

        // This test can't be ok, DomDocument is encoding entities even in comments (╯°□°）╯︵ ┻━┻
        // $this->assertSame("<p>Coucou&hellip;</p> <!-- abusé -->", $fixer->fix("<p>Coucou...</p> <!-- abusé -->"));
    }

    public function testBadRuleSetsWithEmptyArray(): void
    {
        $this->expectException(BadRuleSetException::class);

        new Fixer([]);
    }

    public function testBadRuleSetsWithEmptyArrayAfterConstructor(): void
    {
        $this->expectException(BadRuleSetException::class);

        $fixer = new Fixer(['Ellipsis']);
        $fixer->setRules([]);
    }

    public function testInvalidCustomFixerInstance(): void
    {
        $this->expectException(BadRuleSetException::class);

        new Fixer([new FakeFixer()]);
    }

    public function testOkFixer(): void
    {
        $fixer = new Fixer([new OkFixer()]);

        $this->assertSame('<p>Nope !</p>', $fixer->fix('<p>Nope !</p>'));
    }

    public function testProtectedTags(): void
    {
        $fixer = new Fixer(['Ellipsis']);
        $fixer->setProtectedTags(['pre', 'a']);
        $fixed_content = $fixer->fix('<p>Fixed...</p> <pre>Not fixed...</pre> <p>Fixed... <a>Not Fixed...</a>.</p>');

        $this->assertSame('<p>Fixed&hellip;</p> <pre>Not fixed...</pre> <p>Fixed&hellip; <a>Not Fixed...</a>.</p>', $fixed_content);
    }

    public function testBadClassName(): void
    {
        $this->expectException(BadRuleSetException::class);

        new Fixer(['Ellipsis', 'Acme\\Demo\\Fixer']);
    }

    public function testBadLocale(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $fixer = new Fixer(['Ellipsis']);
        $fixer->setLocale(false);
    }

    public function testEmptyRules(): void
    {
        $this->expectException(BadRuleSetException::class);

        new Fixer([]);
    }

    public function testXmlPrefixedContent(): void
    {
        $fixer = new Fixer(['Ellipsis']);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertSame('<p>Hey &eacute;pic dude&hellip;</p>', $fixer->fix('<?xml encoding="UTF-8"><body><p>Hey épic dude...</p></body>'));
        $this->assertSame('<p>Hey &eacute;pic dude&hellip;</p>', $fixer->fix('<?xml encoding="ISO-8859-1"><body><p>Hey épic dude...</p></body>'));
    }

    public function testBadEncoding(): void
    {
        $fixer = new Fixer(['Trademark']);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertSame('Mentions L&eacute;gales', $fixer->fix('Mentions Légales'));

        // JoliTypo can handle double encoded UTF-8 strings, or ISO strings, but that's not a feature.
        $isoString = mb_convert_encoding('Mentions Légales', 'ISO-8859-1', 'UTF-8');
        $this->assertSame('Mentions L&eacute;gales', $fixer->fix($isoString));
        $this->assertSame('Mentions L&Atilde;&copy;gales', $fixer->fix("Mentions LÃ©gales"));
    }

    public function testEmptyContent(): void
    {
        $fixer = new Fixer(['Trademark']);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertSame('', $fixer->fix(''));
        $this->assertSame("\n ", $fixer->fix("\n "));
        $this->assertSame('some content &reg;', $fixer->fix("\n some content (r)"));
    }

    public function testNonHTMLContent(): void
    {
        $fixer = new Fixer(['Trademark', 'SmartQuotes']);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $toFix = <<<NOT_HTML
            I don't think "FosUserBundle" is a good idea for a complex application.

            \tThat being said, it's an awesome way to get stuffs done(c) in a snap!
            NOT_HTML;
        $fixed = <<<NOT_HTML
            I don't think &ldquo;FosUserBundle&rdquo; is a good idea for a complex application.

            \tThat being said, it's an awesome way to get stuffs done&copy; in a snap!
            NOT_HTML;

        $this->assertSame($fixed, $fixer->fix($toFix));
        $this->assertSame(html_entity_decode($fixed, \ENT_COMPAT, 'UTF-8'), $fixer->fixString($toFix));
        $this->assertSame('Here is a “protip©”!', $fixer->fixString('Here is a "protip(c)"!'));
    }

    /** @group legacy */
    public function testDeprecatedFixer(): void
    {
        $fixer = new Fixer(['Numeric']);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertSame('3' . Fixer::NO_BREAK_SPACE . '€', $fixer->fixString('3 €'));
    }
}

class FakeFixer
{
}

class OkFixer implements FixerInterface
{
    public function fix(string $content, ?StateBag $stateBag = null)
    {
        return $content;
    }
}
