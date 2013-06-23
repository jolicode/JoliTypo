<?php
namespace JoliTypo\Tests;

use JoliTypo\FixerInterface;
use JoliTypo\StateBag;
use JoliTypo\Fixer;

class JoliTypoTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleInstance()
    {
        $fixer = new Fixer();
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals("Coucou&hellip;", $fixer->fix("Coucou..."));
    }

    /**
     * @expectedException JoliTypo\Exception\BadRuleSetException
     */
    public function testBadRuleSets()
    {
        new Fixer('YOLO');
    }

    /**
     * @expectedException JoliTypo\Exception\BadRuleSetException
     */
    public function testBadRuleSetsArray()
    {
        new Fixer(array());
    }

    /**
     * @expectedException JoliTypo\Exception\BadRuleSetException
     */
    public function testBadRuleSetsAfterConstructor()
    {
        $fixer = new Fixer();
        $fixer->setRules('YOLO', array());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidProtectedTags()
    {
        $fixer = new Fixer();
        $fixer->setProtectedTags('YOLO');
    }

    /**
     * @expectedException JoliTypo\Exception\BadRuleSetException
     */
    public function testInvalidCustomFixerInstance()
    {
        $fixer = new Fixer();
        $fixer->setRules('fr', array(new FakeFixer()));
    }

    public function testOkFixer()
    {
        $fixer = new Fixer();
        $fixer->setRules('coucou', array(new OkFixer()));

        $this->assertEquals("<p>Nope !</p>", $fixer->fix("<p>Nope !</p>"));
    }
}

class FakeFixer {}

class OkFixer implements FixerInterface {
    public function fix($content, StateBag $state_bag = null) {
        return $content;
    }
}
