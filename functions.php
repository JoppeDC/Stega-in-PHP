<?php
//Convert string to binary
function toBin($str){
     $str = (string)$str;
     $l = strlen($str);
     $result = '';
     while($l--){
       $result = str_pad(decbin(ord($str[$l])),8,"0",STR_PAD_LEFT).$result;
     }
     return $result;
   }

   //Convert binary to string
function toString($str) {
    $text_array = explode("\r\n", chunk_split($str, 8));
    $newstring = '';
    for ($n = 0; $n < count($text_array) - 1; $n++) {
        $newstring .= chr(base_convert($text_array[$n], 2, 10));
    }
    return $newstring;
}
