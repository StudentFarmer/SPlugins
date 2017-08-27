<?php

namespace solo\smotd\command;

use pocketmine\command\CommandSender;

use solo\smotd\SMotd;
use solo\smotd\SMotdCommand;

class MotdListCommand extends SMotdCommand{

  private $owner;

  public function __construct(SMotd $owner){
    parent::__construct("motdlist", "show all server motd", "/motdlist");
    $this->setPermission("smotd.command.list");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SMotd::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    $motds = $this->owner->getAllMotd();
    $sender->sendMessage("==========[ Motd 목록 (" . count($motds) . ") ]==========");
    foreach($motds as $i => $motd){
      $sender->sendMessage("§7[" . $i . "] §f" . $motd);
    }
    return true;
  }
}
