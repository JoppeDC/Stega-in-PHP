<?php
include('functions.php');


//Edit below variables
$msg = 'Right here you can write te message that you want to encrypt in the img.'; //To encrypt
$src = 'start.jpg'; //Start image

$msg .='|'; //EOF sign, decided to use the pipe symbol to show our decrypter the end of the message
$msgBin = toBin($msg); //Convert our message to binary
$msgLength = strlen($msgBin); //Get message length
$img = imagecreatefromjpeg($src); //returns an image identifier
list($width, $height, $type, $attr) = getimagesize($src); //get image size

if($msgLength>($width*$height)){ //The image has more bits than there are pixels in our image
  echo('Message too long. This is not supported as of now.');
  die();
}

$pixelX=0; //Coordinates of our pixel that we want to edit
$pixelY=0; //^

for($x=0;$x<$msgLength;$x++){ //Encrypt message bit by bit (literally)

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

  $newR = $r; //we dont change the red or green color, only the lsb of blue
  $newG = $g; //^
  $newB = toBin($b); //Convert our blue to binary
  $newB[strlen($newB)-1] = $msgBin[$x]; //Change least significant bit with the bit from out message
  $newB = toString($newB); //Convert our blue back to an integer value (even though its called tostring its actually toHex)

  $new_color = imagecolorallocate($img,$newR,$newG,$newB); //swap pixel with new pixel that has its blue lsb changed (looks the same)
  imagesetpixel($img,$pixelX,$pixelY,$new_color); //Set the color at the x and y positions
  $pixelX++; //next pixel (horizontally)

}
$randomDigit = rand(1,9999); //Random digit for our filename
imagepng($img,'result' . $randomDigit . '.png'); //Create image
echo('done: ' . 'result' . $randomDigit . '.png'); //Echo our image file name

imagedestroy($img); //get rid of it
?>
