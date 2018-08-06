<?php
include('stega.class.php');

$newDecryption = new stega();

$newDecryption->setImage('result3573.png');
$newDecryption->decrypt();
echo($newDecryption->getText());

?>
