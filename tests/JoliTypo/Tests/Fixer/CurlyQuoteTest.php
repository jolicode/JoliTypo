<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;
use PHPUnit\Framework\TestCase;

class CurlyQuoteTest extends TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\CurlyQuote();
        $this->assertInstanceOf('JoliTypo\Fixer\CurlyQuote', $fixer);

        $this->assertSame('This text in which there is a quote: I’m SUPERMAN.', $fixer->fix("This text in which there is a quote: I'm SUPERMAN."));
        $this->assertSame('Swag’', $fixer->fix("Swag'"));
        $this->assertSame('Swag’ me.', $fixer->fix("Swag' me."));
        $this->assertSame('J’aime la typographie.', $fixer->fix("J'aime la typographie."));
        $this->assertSame('Qu’est ce que l’univers ?', $fixer->fix("Qu'est ce que l'univers ?"));
    }

    public function testFalsePositives()
    {
        $fixer = new Fixer\CurlyQuote();

        $this->assertSame("She’s 6' 10\".", $fixer->fix("She's 6' 10\"."));
        $this->assertSame('This is a time: 2"44\'.', $fixer->fix('This is a time: 2"44\'.'));
        $this->assertSame("Here is a crying smiley: :'(", $fixer->fix("Here is a crying smiley: :'("));
    }
}
