<?php

namespace solo\sprefix\command;

use pocketmine\command\CommandSender;

use solo\sprefix\SPrefix;
use solo\sprefix\SPrefixCommand;

class DefaultPrefixCommand extends SPrefixCommand{

  private $owner;

  public function __construct(SPrefix $owner){
    parent::__construct("defaultprefix", "set plugin's default prefix", "/defaultprefix <prefix...>");
    $this->setPermission("sprefix.command.defaultprefix");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SPrefix::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    if(!isset($args[0])){
      $sender->sendMessage(SPrefix::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      $sender->sendMessage(SPrefix::$prefix . "<prefix> 를 reset로 입력시 prefix 설정값을 해제합니다.");
      return true;
    }
    $prefix = implode(" ", $args);
    if($prefix === "reset"){
      $this->owner->setDefaultPrefix(null);
      $sender->sendMessage(SPrefix::$prefix . "Prefix를 원래값으로 사용하도록 설정하였습니다.");
    }else{
      $this->owner->setDefaultPrefix($prefix);
      $sender->sendMessage(SPrefix::$prefix . "Prefix를 \"" . $prefix . "\" 으로 설정하였습니다.");
    }
    $this->owner->updatePrefix();
    $this->owner->save();
    return true;
  }
}
