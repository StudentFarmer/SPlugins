<?php

namespace solo\scolor\color;

class DarkRed implements Color{

  public function getCode(){
    return "4";
  }

  public function getName(){
    return "짙은 빨강";
  }

  public function getPermission(){
    return "scolor.color.darkred";
  }
}
