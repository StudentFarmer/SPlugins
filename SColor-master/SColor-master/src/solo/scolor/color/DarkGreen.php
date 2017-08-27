<?php

namespace solo\scolor\color;

class DarkGreen implements Color{

  public function getCode(){
    return "2";
  }

  public function getName(){
    return "짙은 초록";
  }

  public function getPermission(){
    return "scolor.color.darkgreen";
  }
}
