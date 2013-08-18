<?php
namespace JoliTypo\Tests;

use JoliTypo\FixerInterface;
use JoliTypo\StateBag;
use JoliTypo\Fixer;

class JoliTypoTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleInstance()
    {
        $fixer = new Fixer(array('Ellipsis'));
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals("Coucou&hellip;", $fixer->fix("Coucou..."));
    }

    public function testSimpleInstanceRulesChange()
    {
        $fixer = new Fixer(array('Ellipsis'));
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals("Coucou&hellip;", $fixer->fix("Coucou..."));

        $fixer->setRules(array('CurlyQuote'));

        $this->assertEquals("I&rsquo;m a pony.", $fixer->fix("I'm a pony."));
    }

    public function testHtmlComments()
    {
        $fixer = new Fixer(array('Ellipsis'));
        $this->assertEquals("<p>Coucou&hellip;</p> <!-- Not Coucou... -->", $fixer->fix("<p>Coucou...</p> <!-- Not Coucou... -->"));

        // This test can't be ok, DomDocument is encoding entities even in comments (╯°□°）╯︵ ┻━┻
        //$this->assertEquals("<p>Coucou&hellip;</p> <!-- abusé -->", $fixer->fix("<p>Coucou...</p> <!-- abusé -->"));
    }

    /**
     * @expectedException \JoliTypo\Exception\BadRuleSetException
     */
    public function testBadRuleSets()
    {
        new Fixer('YOLO');
    }

    /**
     * @expectedException \JoliTypo\Exception\BadRuleSetException
     */
    public function testBadRuleSetsArray()
    {
        new Fixer(array());
    }

    /**
     * @expectedException \JoliTypo\Exception\BadRuleSetException
     */
    public function testBadRuleSetsAfterConstructor()
    {
        $fixer = new Fixer(array('Ellipsis'));
        $fixer->setRules('YOLO');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidProtectedTags()
    {
        $fixer = new Fixer(array('Ellipsis'));
        $fixer->setProtectedTags('YOLO');
    }

    /**
     * @expectedException \JoliTypo\Exception\BadRuleSetException
     */
    public function testInvalidCustomFixerInstance()
    {
        new Fixer(array(new FakeFixer()));
    }

    public function testOkFixer()
    {
        $fixer = new Fixer(array(new OkFixer()));

        $this->assertEquals("<p>Nope !</p>", $fixer->fix("<p>Nope !</p>"));
    }

    public function testProtectedTags()
    {
        $fixer          = new Fixer(array('Ellipsis'));
        $fixer->setProtectedTags(array('pre', 'a'));
        $fixed_content  = $fixer->fix("<p>Fixed...</p> <pre>Not fixed...</pre> <p>Fixed... <a>Not Fixed...</a>.</p>");

        $this->assertEquals("<p>Fixed&hellip;</p> <pre>Not fixed...</pre> <p>Fixed&hellip; <a>Not Fixed...</a>.</p>", $fixed_content);
    }

    /**
     * @expectedException \JoliTypo\Exception\BadRuleSetException
     */
    public function testBadClassName()
    {
        new Fixer(array('Ellipsis', 'Acme\\Demo\\Fixer'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadLocale()
    {
        $fixer = new Fixer(array('Ellipsis'));
        $fixer->setLocale(false);
    }

    /**
     * @expectedException \JoliTypo\Exception\BadRuleSetException
     */
    public function testEmptyRules()
    {
        new Fixer(array());
    }

    public function testXmlPrefixedContent()
    {
        $fixer = new Fixer(array('Ellipsis'));
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals("<p>Hey &eacute;pic dude&hellip;</p>", $fixer->fix('<?xml encoding="UTF-8"><body><p>Hey épic dude...</p></body>'));
        $this->assertEquals("<p>Hey &eacute;pic dude&hellip;</p>", $fixer->fix('<?xml encoding="ISO-8859-1"><body><p>Hey épic dude...</p></body>'));
    }
}

class FakeFixer {}

class OkFixer implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        return $content;
    }
}
