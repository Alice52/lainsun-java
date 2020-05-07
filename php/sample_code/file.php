<?php

file_put_contents('./file.txt', 'Hello PHP');

echo file_get_contents('./file.txt');

echo "\n";

var_dump(scandir('.'));