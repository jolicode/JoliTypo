<?php
namespace JoliTypo\Tests;

use JoliTypo\Fixer;

class Html5Test extends \PHPUnit_Framework_TestCase
{
    public function testHtml5Markup()
    {
        $fixer = new Fixer(array(new Fixer\Ellipsis()));
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $html5 = <<<HTML
<header>header</header><aside>Dummy content.</aside><nav><ul><li>Content</li></ul></nav><article><video></video><audio></audio><canvas></canvas><figure>Content</figure></article><footer>footer</footer>
HTML;

        // The test passes if there is no warning about this fix:
        $this->assertEquals($html5, $fixer->fix($html5));
    }
}
