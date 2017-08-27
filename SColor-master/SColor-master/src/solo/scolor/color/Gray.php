<?php

namespace solo\scolor\color;

class Gray implements Color{

  public function getCode(){
    return "7";
  }

  public function getName(){
    return "회색";
  }

  public function getPermission(){
    return "scolor.color.gray";
  }
}
