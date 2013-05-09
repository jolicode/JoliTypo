<?php
namespace JoliTypo\Exception;

class BadRuleSetException extends \Exception
{
    protected $message = "RuleSet must be a supported locale code or an array of Fixer names.";
}
