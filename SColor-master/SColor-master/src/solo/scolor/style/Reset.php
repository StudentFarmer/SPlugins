<?php

namespace solo\scolor\style;

class Reset implements Style{

  public function getCode(){
    return "r";
  }

  public function getName(){
    return "원래대로";
  }

  public function getPermission(){
    return "scolor.color.reset";
  }
}
