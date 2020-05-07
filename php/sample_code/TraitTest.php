<?php

trait WhoTrait
{
    private $who = 'WhoTrait Property';

    public function who()
    {
        echo "WhoTrait\n";
    }
}

// class Test
// {
//     use WhoTrait;
// }
// $test = new Test();
// $test->who();

class Base
{
    public function who()
    {
        echo "Base\n";
    }
}

class SubClass extends Base
{
    use WhoTrait;

    private $who = 'WhoTrait Property';

    public function who()
    {
        echo "SubClass\n";

        echo $this->who . "\n";
    }
}
$subClass = new SubClass();
$subClass->who();