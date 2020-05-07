## type
 * string
  ```php
  $str1 = 'hello {$a}';  //no parse
  $str2 = "hello {$a}"; // parse
  ```
 * array
 ```php
 $array = array(1,2,"a");
 $array = [1,2,"a"];
 $array = [
   'name' => "tom",
   2,
   3 => 'three'
 ];

 // traval the array
 array_walk($arr, function($value){
   // operation each
 });
 ```

## variable
 * prevariable
  - $this
  - $GLOBALS
  - $_SERVER
  - $_GET
  - $_POST
  - $_FILES
  - $_COOKIE
  - $_SESSION
  - $_REQUEST
  - $_ENV
## constant
 * define()
 * const