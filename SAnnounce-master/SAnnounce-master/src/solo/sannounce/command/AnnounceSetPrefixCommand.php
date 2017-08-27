<?php

namespace solo\sannounce\command;

use pocketmine\command\CommandSender;

use solo\sannounce\SAnnounce;
use solo\sannounce\SAnnounceCommand;

class AnnounceSetPrefixCommand extends SAnnounceCommand{

  private $owner;

  public function __construct(SAnnounce $owner){
    parent::__construct("공지접두사", "공지의 접두사를 설정합니다.", "/공지접두사 <접두사...>");
    $this->setPermission("sannounce.command.setprefix");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SAnnounce::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    if(empty($args)){
      $sender->sendMessage(SAnnounce::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $prefix = implode(" ", $args);
    $this->owner->setAnnouncePrefix($prefix);
    $this->owner->save();
    $sender->sendMessage(SAnnounce::$prefix . "공지의 접두사를 변경하였습니다 : " . $prefix);
    return true;
  }
}
