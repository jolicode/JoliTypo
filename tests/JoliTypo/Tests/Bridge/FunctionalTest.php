<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Bridge;

use JoliTypo\Tests\Bridge\app\AppKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class FunctionalTest extends TestCase
{
    public function testRenderTwigViaFilter()
    {
        $kernel = new AppKernel('prod', false);
        $kernel->boot();

        $request = Request::create('/fix');
        $response = $kernel->handle($request);

        self::assertSame(200, $response->getStatusCode());
        $expected = <<<HTML
<p>Raw content: People's.</p>

<p>Fixed content: People&rsquo;s.</p>
HTML;

        self::assertSame($expected, $response->getContent());
    }
}
