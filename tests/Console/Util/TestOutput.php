<?php
namespace Rhumsaa\Uuid\Console\Util;

use Symfony\Component\Console\Output\Output;

class TestOutput extends Output
{
    public $messages = array();

    protected function doWrite($message, $newline)
    {
        $this->messages[] = $message;
    }
}
