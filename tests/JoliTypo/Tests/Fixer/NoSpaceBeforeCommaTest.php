<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

/**
 */
class NoSpaceBeforeCommaTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\NoSpaceBeforeComma();
        $this->assertInstanceOf('JoliTypo\Fixer\NoSpaceBeforeComma', $fixer);

        $this->assertEquals("Superman, you're my hero", $fixer->fix("Superman,you're my hero"));
        $this->assertEquals("Superman, you're my hero", $fixer->fix("Superman ,you're my hero"));
        $this->assertEquals("Superman, you're my hero", $fixer->fix("Superman  ,  you're my hero"));
        $this->assertEquals("F, bar", $fixer->fix("F,bar"));
        $this->assertEquals("Seule 1,7 million de personnes", $fixer->fix("Seule 1,7 million de personnes"));
    }
}
