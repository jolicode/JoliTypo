<?php
namespace JoliTypo\Bridge\Symfony\Twig;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class JoliTypoExtension extends \Twig_Extension
{
    private $presets = array();

    public function __construct($presets)
    {
        $this->presets = $presets;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('jolitypo', array($this, 'translate')),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('jolitypo', array($this, 'translate'), array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    public function translate($text, $preset = "default")
    {
        if (!isset($this->presets[$preset])) {
            throw new InvalidConfigurationException(sprintf("There is no '%s' preset configured.", $preset));
        }

        return $this->presets[$preset]->fix($text);
    }

    public function getName()
    {
        return 'jolitypo';
    }
}
