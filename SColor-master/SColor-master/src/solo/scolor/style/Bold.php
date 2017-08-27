<?php

namespace solo\scolor\style;

class Bold implements Style{

  public function getCode(){
    return "l";
  }

  public function getName(){
    return "굵게";
  }

  public function getPermission(){
    return "scolor.color.bold";
  }
}
