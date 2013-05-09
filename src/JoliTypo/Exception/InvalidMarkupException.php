<?php
namespace JoliTypo\Exception;

class InvalidMarkupException extends \DOMException
{
    protected $message = "An error happened when trying to read your HTML with \\DOMDocument.";
}
