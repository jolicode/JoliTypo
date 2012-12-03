<?php

include(dirname(__FILE__).'/../vendor/php-typography/php-typography.php');

class phpTypographyTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Obsolete.');
    }

    public function testBasicHtml()
    {
        $typo = new phpTypography();

        //$this->set_diacritic_language();
        $typo->set_hyphenation_language('fr');

        $html = $typo->process("<p>It's a pony !</p>");
        $this->assertEquals("<p>It’s a pony !</p>", $html);

        $html = $typo->process('<p>I like "unicorn".</p>');
        $this->assertEquals('<p>I like « unicorn ».</p>', $html);

        $html = $typo->process("<p>Testing like a boss...</p>");
        $this->assertEquals("<p>Testing like a boss...</p>", $html);
    }
}
