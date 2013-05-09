<?php
namespace JoliTypo\Tests;

class JoliTypoTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterProvider()
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
        $fixer->setRules('YOLO');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidProtectedTags()
    {
        $fixer = new \JoliTypo\Fixer();
        $fixer->setProtectedTags('YOLO');
    }
}
