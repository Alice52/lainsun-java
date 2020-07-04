<?php
function error_handler($errNo, $errMsg, $fileName, $lineNum)
{
    $errorType = [
        E_ERROR              => 'Error',
        E_PARSE              => 'Parsing Error',
        E_CORE_ERROR         => 'Core Error',
        E_CORE_WARNING       => 'Core Warning',
        E_COMPILE_ERROR      => 'Compile Error',
        E_COMPILE_WARNING    => 'Compile Warning',
        E_WARNING            => 'Warning',
        E_NOTICE             => 'Notice',
        E_USER_ERROR         => 'User Error',
        E_USER_WARNING       => 'User Warning',
        E_USER_NOTICE        => 'User Notice',
        E_STRICT             => 'Runtime Notice',
        E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
    ];

    echo 'Error: ' . $errorType[$errNo] . ', Msg: ' . $errMsg;
}

set_error_handler('error_handler');

trigger_error('error test', E_USER_ERROR);