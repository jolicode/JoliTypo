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

class FrenchNoBreakSpaceTest extends TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\FrenchNoBreakSpace();
        $this->assertInstanceOf('JoliTypo\Fixer\FrenchNoBreakSpace', $fixer);

        $this->assertEquals('Superman' . Fixer::NO_BREAK_THIN_SPACE . '!', $fixer->fix('Superman !'));
        $this->assertEquals('Superman' . Fixer::NO_BREAK_THIN_SPACE . '?', $fixer->fix('Superman ?'));
        $this->assertEquals('Superman' . Fixer::NO_BREAK_THIN_SPACE . '!?', $fixer->fix('Superman !?'));
        $this->assertEquals('Superman' . Fixer::NO_BREAK_THIN_SPACE . '? Nope.', $fixer->fix('Superman ? Nope.'));
        $this->assertEquals('Superman' . Fixer::NO_BREAK_SPACE . ': the movie', $fixer->fix('Superman : the movie'));
        $this->assertEquals('Superman: the movie', $fixer->fix('Superman: the movie'));
        $this->assertEquals('Superman' . Fixer::NO_BREAK_THIN_SPACE . '; the movie', $fixer->fix('Superman ; the movie'));
        $this->assertEquals('Superman' . Fixer::NO_BREAK_THIN_SPACE . '; the movie', $fixer->fix("Superman\u{a0}; the movie"));
        $this->assertEquals('fdda:5cc1:23:4::1f', $fixer->fix('fdda:5cc1:23:4::1f'));

        $this->assertEquals('Here is a  brand name: Yahoo!', $fixer->fix('Here is a  brand name: Yahoo!'));
    }
}
