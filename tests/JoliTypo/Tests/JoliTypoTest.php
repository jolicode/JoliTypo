<?php

class JoliTypoTest extends PHPUnit_Framework_TestCase
{
    public function testRegisterProvider()
    {
        $this->assertInstanceOf('JoliTypo\Fixer', new \JoliTypo\Fixer());
    }
}
