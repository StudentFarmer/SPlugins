<?php

namespace solo\scolor\color;

class Yellow implements Color{

  public function getCode(){
    return "e";
  }

  public function getName(){
    return "노랑";
  }

  public function getPermission(){
    return "scolor.color.yellow";
  }
}
