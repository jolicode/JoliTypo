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

class DimensionTest extends TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\Dimension();
        $this->assertInstanceOf('JoliTypo\Fixer\Dimension', $fixer);

        $this->assertEquals('Test', $fixer->fix('Test'));
        $this->assertEquals('TestxTest', $fixer->fix('TestxTest'));
        $this->assertEquals('Here is a calcul: 99'.Fixer::TIMES.'122.21', $fixer->fix('Here is a calcul: 99x122.21'));
        $this->assertEquals('Here is a calcul: 99 '.Fixer::TIMES.' 122.21', $fixer->fix('Here is a calcul: 99 x 122.21'));
        $this->assertEquals('Here is a calcul: 99 1212,32 '.Fixer::TIMES.' 122.21', $fixer->fix('Here is a calcul: 99 1212,32 x 122.21'));
        $this->assertEquals('3 '.Fixer::TIMES.' 1', $fixer->fix('3 x 1'));
        $this->assertEquals('8.5"'.Fixer::TIMES.'10"', $fixer->fix('8.5"x10"'));
        $this->assertEquals('8.5" '.Fixer::TIMES.' 10"', $fixer->fix('8.5" x 10"'));
    }
}
