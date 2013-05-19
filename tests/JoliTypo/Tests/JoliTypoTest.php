<?php
namespace JoliTypo\Tests;

use JoliTypo\FixerInterface;

class JoliTypoTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleInstance()
    {
        $fixer = new \JoliTypo\Fixer();
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals("Coucou&hellip;", $fixer->fix("Coucou..."));
    }

    /**
     * @expectedException JoliTypo\Exception\BadRuleSetException
     */
    public function testBadRuleSets()
    {
        new \JoliTypo\Fixer('YOLO');
    }

    /**
     * @expectedException JoliTypo\Exception\BadRuleSetException
     */
    public function testBadRuleSetsArray()
    {
        new \JoliTypo\Fixer(array());
    }

    /**
     * @expectedException JoliTypo\Exception\BadRuleSetException
     */
    public function testBadRuleSetsAfterConstructor()
    {
        $fixer = new \JoliTypo\Fixer();
        $fixer->setRules('YOLO', array());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidProtectedTags()
    {
        $fixer = new \JoliTypo\Fixer();
        $fixer->setProtectedTags('YOLO');
    }

    /**
     * @expectedException JoliTypo\Exception\BadRuleSetException
     */
    public function testInvalidCustomFixerInstance()
    {
        $fixer = new \JoliTypo\Fixer();
        $fixer->setRules('fr', array(new FakeFixer()));
    }

    public function testOkFixer()
    {
        $fixer = new \JoliTypo\Fixer();
        $fixer->setRules('coucou', array(new OkFixer()));

        $this->assertEquals("<p>Nope !</p>", $fixer->fix("<p>Nope !</p>"));
    }
}

class FakeFixer {}

class OkFixer implements FixerInterface {
    public function fix($content) {
        return $content;
    }
}
