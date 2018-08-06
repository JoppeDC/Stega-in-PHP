<?php
include('stega.class.php');

$newEncryption = new stega();

$newEncryption->setText('Right here you can write te message that you want to encrypt in the img.');
$newEncryption->setImage('start.jpg');
$newEncryption->encrypt();

?>
