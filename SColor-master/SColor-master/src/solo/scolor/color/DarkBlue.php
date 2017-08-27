<?php

namespace solo\scolor\color;

class DarkBlue implements Color{

  public function getCode(){
    return "1";
  }

  public function getName(){
    return "짙은 파랑";
  }

  public function getPermission(){
    return "scolor.color.darkblue";
  }
}
