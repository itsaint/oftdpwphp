<?php

class OftIDGen{
  public static function get_new_id(){
    $id = rand(10,99);
    $file = fopen("cids.txt","r");
    $i = 0;
    if($file){
      while(($buffer = fgets($file)) !== false){
        $i++;
        if($id == (int)rtrim($buffer) ){
          fclose($file);
          if($i > 7) {
            throw new Exception("no more ids available");
          }
          return  self::get_new_id();
        }
      }
    }
    file_put_contents("cids.txt", $id."\n", FILE_APPEND | LOCK_EX);
    return $id;
  }
}