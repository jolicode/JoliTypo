<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Fixer;

use JoliTypo\Exception\BadFixerConfigurationException;
use JoliTypo\Fixer;
use PHPUnit\Framework\TestCase;

class SmartQuotesTest extends TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\SmartQuotes('de');
        $this->assertInstanceOf('JoliTypo\Fixer\SmartQuotes', $fixer);

        $this->assertEquals('„I am smart“', $fixer->fix('"I am smart"'));

        $fixer->setOpening('«');
        $fixer->setClosing('»');

        $this->assertEquals('«I am smart»', $fixer->fix('"I am smart"'));

        $fixer->setOpening('<');
        $fixer->setClosing('>');

        $this->assertEquals('<I am smart>', $fixer->fix('"I am smart"'));
    }

    public function testBadConfig()
    {
        $this->expectException(BadFixerConfigurationException::class);

        $fixer = new Fixer\SmartQuotes('unknown');
        $fixer->fix('nope');
    }
}
