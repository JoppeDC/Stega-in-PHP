<?php
class stega {

    //Class variables
    private $image;
    private $text;

    //Getters and Setters
    function setImage($image) {
        $this->image = $image;
    }
    function setText($text) {
        $this->text = $text;
    }

    function getText() {
        return $this->text;
    }

    function encrypt(){
      $msg = $this->text;
      $src = $this->image;

      $msg .= '|'; //EOF sign, decided to use the pipe symbol to show our decrypter the end of the message
      $msgBin = $this->toBin($msg); //Convert our message to binary
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
        $newB = $this->toBin($b); //Convert our blue to binary
        $newB[strlen($newB)-1] = $msgBin[$x]; //Change least significant bit with the bit from out message
        $newB = $this->toString($newB); //Convert our blue back to an integer value (even though its called tostring its actually toHex)

        $new_color = imagecolorallocate($img,$newR,$newG,$newB); //swap pixel with new pixel that has its blue lsb changed (looks the same)
        imagesetpixel($img,$pixelX,$pixelY,$new_color); //Set the color at the x and y positions
        $pixelX++; //next pixel (horizontally)

      }
      $randomDigit = rand(1,9999); //Random digit for our filename
      imagepng($img,'result' . $randomDigit . '.png'); //Create image
      echo('done: ' . 'result' . $randomDigit . '.png'); //Echo our image file name

      imagedestroy($img); //get rid of it
    }

    function decrypt(){
      $src = $this->image;


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

        $blue = $this->toBin($b); //Convert our blue to binary

        $real_message .= $blue[strlen($blue) - 1]; //Ad the lsb to our binary result

        $count++; //Coun that a digit was added

        if ($count == 8) { //Every time we hit 8 new digits, check the value
            if ($this->toString(substr($real_message, -8)) === '|') { //Whats the value of the last 8 digits?
                $this->text = $this->toString(substr($real_message,0,-8)); //convert to string and remove /
                break;
            }
            $count = 0; //Reset counter
        }

        $pixelX++; //Change x coordinates to next
      }
    }


    //Convert string to binary
    private function toBin($str){
         $str = (string)$str;
         $l = strlen($str);
         $result = '';
         while($l--){
           $result = str_pad(decbin(ord($str[$l])),8,"0",STR_PAD_LEFT).$result;
         }
         return $result;
       }

       //Convert binary to string
    private function toString($str) {
        $text_array = explode("\r\n", chunk_split($str, 8));
        $newstring = '';
        for ($n = 0; $n < count($text_array) - 1; $n++) {
            $newstring .= chr(base_convert($text_array[$n], 2, 10));
        }
        return $newstring;
    }

}

 ?>
