<?php

namespace solo\scolor\color;

class DarkGray implements Color{

  public function getCode(){
    return "8";
  }

  public function getName(){
    return "짙은 회색";
  }

  public function getPermission(){
    return "scolor.color.darkgray";
  }
}
