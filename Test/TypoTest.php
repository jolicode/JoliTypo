<?php
namespace Joli\Test;

class FixerTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicHtml()
    {
        $typo = new \Joli\Typo\Fixer();
        var_dump(
            $typo->parse('<p>Coucou <a href="">coucou</a> LOL <pre>Protected text</pre></p>')
        );
    }
}
