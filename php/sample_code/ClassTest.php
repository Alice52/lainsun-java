<?php
// class A
// {
//     private $type;
//     const TYPE = 'A';

//     public function __construct()
//     {
//         $this->type = 'A';

//         echo "constructing {$this->type}\n";
//     }

//     public function __destruct()
//     {
//         echo "Destroying A\n";
//     }

//     public function hi()
//     {
//         echo 'method hi run: ' . self::TYPE . "\n";
//     }

//     public static function staticFun()
//     {
//         echo 'method staticFun run: ' . self::TYPE . "\n";
//     }
// }
// $a = new A();
// $a->hi();
// echo A::staticFun();
// echo A::TYPE;

// class B extends A
// {
//     public function __construct()
//     {
//         parent::__construct();

//         echo "constructing B\n";
//     }

//     public function hi()
//     {
//         // parent::hi();

//         echo "Hello B\n";
//     }
// }
// $b = new B();
// $b->hi();

// include './User.php';
// $user = new User('Tom', 21);
// $user->info();

// spl_autoload_register(function($className) {
//     $filename = './' . $className . '.php';

//     if(is_file($filename)) {
//         include $filename;
//         return;
//     }
// });
// $user = new User('Jerry', 20);
// $user->info();
