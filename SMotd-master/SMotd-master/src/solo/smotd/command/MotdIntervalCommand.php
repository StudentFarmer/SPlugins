<?php

namespace solo\smotd\command;

use pocketmine\command\CommandSender;

use solo\smotd\SMotd;
use solo\smotd\SMotdCommand;

class MotdIntervalCommand extends SMotdCommand{

  private $owner;

  public function __construct(SMotd $owner){
    parent::__construct("motdinterval", "set interval of motd", "/motdinterval <second>");
    $this->setPermission("smotd.command.interval");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SMotd::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    if(!preg_match("/[0-9]+/", $args[0] ?? "NaN") || $args[0] == 0){
      $sender->sendMessage(SMotd::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $interval = intval($args[0]) * 20;
    $this->owner->setChangeInterval($interval);
    $this->owner->save();
    $sender->sendMessage(SMotd::$prefix . "Motd 변경 주기를 설정하였습니다 : " . ($interval / 20) . "초");
    return true;
  }
}
