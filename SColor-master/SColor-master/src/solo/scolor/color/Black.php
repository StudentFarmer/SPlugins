<?php

namespace solo\scolor\color;

class Black implements Color{

  public function getCode(){
    return "0";
  }

  public function getName(){
    return "검정";
  }

  public function getPermission(){
    return "scolor.color.black";
  }
}
