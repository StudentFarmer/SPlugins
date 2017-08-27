<?php

namespace solo\scolor\command;

use pocketmine\command\CommandSender;

use solo\scolor\SColor;
use solo\scolor\SColorCommand;

class ColorCommand extends SColorCommand{

  private $owner;

  public function __construct(SColor $owner){
    parent::__construct("색상표", "사용 가능한 색 목록을 확인합니다.", "/색상표", ["색", "색목록"]);
    $this->setPermission("scolor.command.color");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    $sender->sendMessage("§l==========[ 사용 가능한 색 목록 ]==========");

    $availableColors = array_filter($this->owner->getRegisteredColors(), function($color) use ($sender){ return $sender->hasPermission($color->getPermission()); });
    $sender->sendMessage(implode(", ", array_map(function($color){ return $color->getName() . " (색코드 : " . $color->getCode() . ", §" . $color->getCode() . "예시/Example§r§f)"; }, $availableColors)));

    $sender->sendMessage(" ");

    $sender->sendMessage("§l==========[ 사용 가능한 스타일 목록 ]==========");

    $availableStyles = array_filter($this->owner->getRegisteredStyles(), function($style) use ($sender){ return $sender->hasPermission($style->getPermission()); });
    $sender->sendMessage(implode(", ", array_map(function($style){ return $style->getName() . " (색코드 : " . $style->getCode() . ", §" . $style->getCode() . "예시/Example§r§f)"; }, $availableStyles)));

    return true;
  }
}
