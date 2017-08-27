<?php

namespace solo\smotd\command;

use pocketmine\command\CommandSender;

use solo\smotd\SMotd;
use solo\smotd\SMotdCommand;

class MotdRemoveCommand extends SMotdCommand{

  private $owner;

  public function __construct(SMotd $owner){
    parent::__construct("motdremove", "remove server motd", "/motdremove <index>");
    $this->setPermission("smotd.command.remove");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SMotd::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    if(!preg_match("/[0-9]+/", $args[0] ?? "NaN")){
      $sender->sendMessage(SMotd::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $index = intval($args[0]);
    if($this->owner->removeMotd($index)){
      $this->owner->save();
      $sender->sendMessage(SMotd::$prefix . "Motd를 제거하였습니다.");
    }else{
      $sender->sendMessage(SMotd::$prefix . "해당 인덱스는 존재하지 않습니다.");
    }
    return true;
  }
}
