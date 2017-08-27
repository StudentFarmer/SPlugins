<?php

namespace solo\scolor\style;

class Obfuscated implements Style{

  public function getCode(){
    return "k";
  }

  public function getName(){
    return "무작위";
  }

  public function getPermission(){
    return "scolor.color.obfuscated";
  }
}
