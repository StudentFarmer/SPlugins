<?php

namespace solo\scolor\color;

class Blue implements Color{

  public function getCode(){
    return "9";
  }

  public function getName(){
    return "파랑";
  }

  public function getPermission(){
    return "scolor.color.blue";
  }
}
