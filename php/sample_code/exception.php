<?php
set_exception_handler(function($e) {
    echo "Handle Exception: ", $e->getMessage(), "\n";
});

class MyException extends Exception
{
    public function __construct($msg = '')
    {
        parent::__construct("MyException: [$msg]");
    }
}

throw new MyException('test');

echo "Not Executed\n";