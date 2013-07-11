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
}

class FakeFixer {}

class OkFixer implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        return $content;
    }
}
