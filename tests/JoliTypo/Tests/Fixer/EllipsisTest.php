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

class EllipsisTest extends TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\Ellipsis();
        $this->assertInstanceOf('JoliTypo\Fixer\Ellipsis', $fixer);

        $this->assertEquals('Test', $fixer->fix('Test'));
        $this->assertEquals('Test…', $fixer->fix('Test...'));
        $this->assertEquals('Am I an ellipsis?', $fixer->fix('Am I an ellipsis?'));
        $this->assertEquals('Am I an ellipsis..', $fixer->fix('Am I an ellipsis..'));
        $this->assertEquals('Am I an ellipsis…', $fixer->fix('Am I an ellipsis....'));
        $this->assertEquals('Am I an ellipsis…', $fixer->fix('Am I an ellipsis…'));
        $this->assertEquals('Am I an ellipsis… With following text.', $fixer->fix('Am I an ellipsis... With following text.'));
    }
}
