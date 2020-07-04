<?php

/* String */
// $a = 'PHP';
// $str1 = 'hello {$a}';  //no parse
// $str2 = "hello {$a}";
// $str3 = <<<LABEL
// heredoc syntax.
// hello {$a}
// LABEL;
// $str4 = <<<'EOD'
// nowdoc syntax.
// hello {$a}
// EOD;
// echo $str4;

/* Array */
// $arr1 = array(1, 2, 'hello');
// $arr2 = [1, 2, 'hello'];
// $arr3 = [
//     'name' => 'Tom',
//     2,
//     3 => 'three'
// ];
// var_dump($arr3);


// require './var.php';
// echo "after include";


/* Functions */
// function fun()
// {
//     echo 'this is fun';
// }
// function say($who)
// {
//     echo "hi, {$who}";
// }
// function arrLen(array $arr = [])
// {
//     $count = count($arr);

//     echo "count: {$count}";
// }
// fun();
// say('PHP');
// arrLen();
// arrLen([1, 2]);
// arrLen('test');

// $varFun = 'arrLen';
// $varFun();

$arr = [1, 2];
array_walk($arr, function($value)
{
    echo $value . ' ';
});
