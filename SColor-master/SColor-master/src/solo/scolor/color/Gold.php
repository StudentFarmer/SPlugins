<?php

namespace solo\scolor\color;

class Gold implements Color{

  public function getCode(){
    return "6";
  }

  public function getName(){
    return "주황";
  }

  public function getPermission(){
    return "scolor.color.gold";
  }
}
