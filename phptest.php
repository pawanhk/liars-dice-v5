<?php

$string = "test";
$arr = str_split($string);
print_r($arr);

$st_arr = implode("", $arr);
echo $st_arr;


?>