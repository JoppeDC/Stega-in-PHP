<?php
include('functions.php');

$src = 'result3573.png'; //Change this to the image to decrypt


$img = imagecreatefrompng($src); //Returns image identifier
$real_message = ''; //Empty variable to store our message

$count = 0; //Wil be used to check our last char
$pixelX = 0; //Start pixel x coordinates
$pixelY = 0; //start pixel y coordinates

list($width, $height, $type, $attr) = getimagesize($src); //get image size

for ($x = 0; $x < ($width*$height); $x++) { //Loop through pixel by pixel
  if($pixelX === $width+1){ //If this is true, we've reached the end of the row of pixels, start on next row
    $pixelY++;
    $pixelX=0;
  }

  if($pixelY===$height && $pixelX===$width){ //Check if we reached the end of our file
    echo('Max Reached');
    die();
  }

  $rgb = imagecolorat($img,$pixelX,$pixelY); //Color of the pixel at the x and y positions
  $r = ($rgb >>16) & 0xFF; //returns red value for example int(119)
  $g = ($rgb >>8) & 0xFF; //^^ but green
  $b = $rgb & 0xFF;//^^ but blue

  $blue = toBin($b); //Convert our blue to binary

  $real_message .= $blue[strlen($blue) - 1]; //Ad the lsb to our binary result

  $count++; //Coun that a digit was added

  if ($count == 8) { //Every time we hit 8 new digits, check the value
      if (toString(substr($real_message, -8)) === '|') { //Whats the value of the last 8 digits?
          echo ('done<br>'); //Yes we're done now
          $real_message = toString(substr($real_message,0,-8)); //convert to string and remove /
          echo ('Result: ');
          echo $real_message; //Show
          die;
      }
      $count = 0; //Reset counter
  }

  $pixelX++; //Change x coordinates to next
}
?>
