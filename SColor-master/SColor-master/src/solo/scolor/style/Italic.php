<?php

namespace solo\scolor\style;

class Italic implements Style{

  public function getCode(){
    return "o";
  }

  public function getName(){
    return "기울이기";
  }

  public function getPermission(){
    return "scolor.color.italic";
  }
}
