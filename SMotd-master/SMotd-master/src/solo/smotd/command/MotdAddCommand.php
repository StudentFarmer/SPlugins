<?php

namespace solo\smotd\command;

use pocketmine\command\CommandSender;

use solo\smotd\SMotd;
use solo\smotd\SMotdCommand;

class MotdAddCommand extends SMotdCommand{

  private $owner;

  public function __construct(SMotd $owner){
    parent::__construct("motdadd", "add server motd", "/motdadd <motd...>");
    $this->setPermission("smotd.command.add");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SMotd::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    $motd = implode(" ", $args);
    if(trim($motd) == ""){
      $sender->sendMessage(SMotd::$prefix . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $this->owner->addMotd($motd);
    $this->owner->save();
    $sender->sendMessage(SMotd::$prefix . "Motd를 추가하였습니다 : " . $motd);
    return true;
  }
}
