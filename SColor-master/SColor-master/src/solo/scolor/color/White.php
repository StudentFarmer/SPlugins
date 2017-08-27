<?php

namespace solo\scolor\color;

class White implements Color{

  public function getCode(){
    return "f";
  }

  public function getName(){
    return "하양";
  }

  public function getPermission(){
    return "scolor.color.white";
  }
}
