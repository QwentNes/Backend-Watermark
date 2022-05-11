<?php

class Generate {
  private static $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

  public static function FileName($length = 20){
    $charactersLength = strlen(self::$characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= self::$characters[rand(0, $charactersLength - 1)];
    }
    return $randomString."_".Time();
  }

  public static function image($path){
      if(file_exists($path)){
          header('Content-type: image/png');
          $img = imagecreatefrompng($path);
          imagealphablending($img, false);
          imagesavealpha($img, true);
          imagepng($img);
          imagedestroy($img);
      }
      else{
          response()->markup("Not found", 404);
      }
  }
}
