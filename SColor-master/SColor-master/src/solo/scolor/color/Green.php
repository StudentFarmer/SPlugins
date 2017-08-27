<?php

namespace solo\scolor\color;

class Green implements Color{

  public function getCode(){
    return "a";
  }

  public function getName(){
    return "연두";
  }

  public function getPermission(){
    return "scolor.color.green";
  }
}
