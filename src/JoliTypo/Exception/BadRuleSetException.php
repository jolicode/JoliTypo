<?php
namespace JoliTypo\Exception;

class BadRuleSetException extends \Exception
{
    protected $message = "RuleSet must be an array of Fixer names or instances.";
}
