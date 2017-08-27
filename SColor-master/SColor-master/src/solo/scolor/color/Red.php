<?php

namespace solo\scolor\color;

class Red implements Color{

  public function getCode(){
    return "c";
  }

  public function getName(){
    return "빨강";
  }

  public function getPermission(){
    return "scolor.color.red";
  }
}
