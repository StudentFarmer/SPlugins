<?php

namespace solo\sannounce\command;

use pocketmine\command\CommandSender;

use solo\sannounce\SAnnounce;
use solo\sannounce\SAnnounceCommand;

class AnnounceRemoveCommand extends SAnnounceCommand{

  private $owner;

  public function __construct(SAnnounce $owner){
    parent::__construct("공지삭제", "해당 인덱스의 공지를 삭제합니다.", "/공지삭제 <인덱스>");
    $this->setPermission("sannounce.command.remove");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SAnnounce::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    if(empty($args) || !preg_match("/[0-9]+/", $args[0])){
      $sender->sendMessage(SAnnounce::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $index = intval($args[0]);
    $removed = $this->owner->removeAnnounce($index);
    $this->owner->save();

    if($removed !== null){
      $sender->sendMessage(SAnnounce::$prefix . "공지를 삭제하였습니다 : " . $removed);
    }else{
      $sender->sendMessage(SAnnounce::$prefix . "해당 인덱스는 존재하지 않습니다.");
    }
    return true;
  }
}
