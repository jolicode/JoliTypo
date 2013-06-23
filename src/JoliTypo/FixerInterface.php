<?php
namespace JoliTypo;

interface FixerInterface
{
    /**
     * @param  string   $content   A string to fix
     * @param  StateBag $state_bag A bag of useful informations
     * @return string
     */
    public function fix($content, StateBag $state_bag = null);
}
