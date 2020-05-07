<?php
class MagicFun
{
    private function print($msg)
    {
        echo "$msg\n";
    }

    public function __call($name, $arguments)
    {
        echo 'method [' . $name . '] is called!' . "\n";

        if (method_exists($this, $name)) {
            call_user_func_array([$this, $name], $arguments);
        }
    }
}
$magicFun = new MagicFun();
// $magicFun->print('hello PHP!');
$magicFun->fun();